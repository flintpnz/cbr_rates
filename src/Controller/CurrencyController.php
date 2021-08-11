<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Entity\ExchangeRate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CurrencyController extends AbstractController
{
    /**
     * @Route("/currency/update", methods={"get"})
     */
    public function updateAction(Request $request, ValidatorInterface $validator, PaginatorInterface $paginator): Response
    {
        /*
         * Пакетный импорт курсов валют с сайта cbr.ru из XML с даты $start_date по дату $end_date
         * Если на импортируемую дату в БД курс существует - то он обновляется, иначе добавляется новый
         */
        $start_date = \DateTime::createFromFormat('Y-m-d', $request->query->get('from'));
        $end_date = \DateTime::createFromFormat('Y-m-d', $request->query->get('to'));
        $doctrine = $this->getDoctrine();
        $date = clone $start_date;
        while ($date <= $end_date)
        {
            $data = file_get_contents("https://www.cbr.ru/scripts/XML_daily.asp?date_req=".$date->format('d')."/".$date->format('m')."/".$date->format('Y'));
            $xml = new \SimpleXMLElement($data);
            $xml_date = \DateTime::createFromFormat('d.m.Y', $xml->attributes()['Date']->__toString());
            // Проверка на соответствие даты в XML запрашиваемой дате (за будущие даты отдается последний доступный курс вместо 404)
            if ($xml_date->format('d-m-Y') == $date->format('d-m-Y'))
            {
                foreach ($xml->children() as $item){
                    foreach ($item as $element){
                        if($element->getName()=="Nominal")
                            $nominal = $element->__toString();
                        if($element->getName()=="Value")
                            $value = $element->__toString();
                    }
                    $id = $doctrine->getRepository(Currency::class)->findOneBy(['id_currency' => $item->attributes()['ID']->__toString()]);
                    $rate = $doctrine->getRepository(ExchangeRate::class)->findOneBy(array('date' => $date, 'id_currency' => $id->getId()));
                    // Проверка существования курса в БД
                    if (empty($rate))
                        $rate = new ExchangeRate();
                    $rate->setIdCurrency($id);
                    $rate->setNominal($nominal);
                    $rate->setDate($date);
                    $rate->setValue(str_replace(',','.',$value));
                    $doctrine->getManager()->persist($rate);
                    $doctrine->getManager()->flush();
                }
            }
            $date->modify('+1 day');
        }

        $all_currencies = $doctrine->getRepository(Currency::class)->findBy([], ['name' => 'ASC']);
        $order = $doctrine->getRepository(Currency::class)->getOrder($request->query->get('order'));

        return $this->render('currency/update.html.twig', [
            'start_date' => $start_date->format('Y-m-d'),
            'end_date' => $end_date->format('Y-m-d'),
            'currencies' => $all_currencies, // Список всех валют для селектора
            'reverse' => $order != 'ASC', // Флаг сортировки для форм
        ]);
    }

    /**
     * @Route("/currency/{id}", methods={"get"})
     */
    public function currencyIdAction($id, Request $request, PaginatorInterface $paginator): Response
    {
        /*
         * Подготовка данных (все, либо с $start_date по $end_date) и передача их во View для отображения:
         * либо default/index.html.twig в случае полного списка валют ($id=0)
         * либо currency/currency_id.html.twig в случае конкретной валюты $id
         */
        $start_date = \DateTime::createFromFormat('Y-m-d', $request->query->get('from'));
        $end_date = \DateTime::createFromFormat('Y-m-d', $request->query->get('to'));
        $doctrine = $this->getDoctrine();

        $order = $doctrine->getRepository(Currency::class)->getOrder($request->query->get('order'));

        if ($id == 0) // Подготовка запроса для всех валют
        {
            if ($start_date != null and $end_date != null) // За определенный диапазон
                $queryBuilder = $doctrine->getRepository(ExchangeRate::class)->findBetweenDatesAllCurrenciesPage($start_date, $end_date, $order);
            else // За все время
            {
                $start_date = $end_date = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
                $queryBuilder = $doctrine->getRepository(ExchangeRate::class)->findAllCurrenciesPage($order);
            }
        }
        else // Конкретная валюта $id
        {
            // Получение валюты
            $currency = $doctrine->getRepository(Currency::class)->findOneBy(['id' => $id]);
            if ($start_date != null and $end_date != null) // За определенный диапазон
                $queryBuilder = $doctrine->getRepository(ExchangeRate::class)->findBetweenDatesOneCurrencyPage($currency, $start_date, $end_date, $order);
            else // За все время
            {
                $start_date = $end_date = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
                $queryBuilder = $doctrine->getRepository(ExchangeRate::class)->findAllOneCurrencyPage($currency, $order);
            }
        }

        // Пагинация результатов, 34 - количество актуальных валют на 11.08.2021
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            34
        );
        $pagination->setTemplate('pagination.html.twig');

        $all_currencies = $doctrine->getRepository(Currency::class)->findBy([], ['name' => 'ASC']);

        if ($id == 0) // View для всех валют
            return $this->render('default/index.html.twig', [
                'start_date' => $start_date->format('Y-m-d'),
                'end_date' => $end_date->format('Y-m-d'),
                'pagination' => $pagination,
                'currencies' => $all_currencies, // Список всех валют для селектора
                'reverse' => $order != 'ASC', // Флаг сортировки для форм
            ]);
        else // View для конкретной валюты
            return $this->render('currency/currency_id.html.twig', [
                'start_date' => $start_date->format('Y-m-d'),
                'end_date' => $end_date->format('Y-m-d'),
                'currency' => $currency, // Валюта для форм
                'pagination' => $pagination,
                'currencies' => $all_currencies, // Список всех валют для селектора
                'reverse' => $order != 'ASC', // Флаг сортировки для форм
            ]);
    }

}
