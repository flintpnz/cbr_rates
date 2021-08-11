<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Entity\ExchangeRate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", methods={"get"})
     */
    public function indexAction(Request $request, PaginatorInterface $paginator): Response
    {
        /*
         * Главная страница
         * Проверка есть ли в GET $id валюты, входящий в список доступных для отображения
         * И передача управления контроллеру currencyIdAction для дальнейшей работы
         * Иначе редирект на главную при некорректном $id
         */
        $id = $request->query->get('id');
        $count = $this->getDoctrine()->getRepository(Currency::class)->countCurrencies();
        if ($id == null)
            $id = 0;
        if ($id >= 0 and $id <= $count)
            return $this->forward('App\Controller\CurrencyController:currencyIdAction', array(
                'id'  => $request->query->get('id'),
                'request' => $request,
            ));
        else
            return $this->redirect('/', 301);
    }

    /**
     * @Route("/json", methods={"get"})
     */
    public function jsonAction(Request $request, PaginatorInterface $paginator): Response
    {
        /*
         * Проверяется наличие в GET: дат, параметра экспорта в json id и его вхождения в доступный список валют
         * В случае успеха передача управления контроллеру jsonIdAction
         * Иначе редирект на главную
         */
        $start_date = \DateTime::createFromFormat('Y-m-d', $request->query->get('from'));
        $end_date = \DateTime::createFromFormat('Y-m-d', $request->query->get('to'));
        if ($start_date == null or $end_date == null or strtolower($request->query->get('export')) != 'json')
        {
            return $this->redirect('/', 301);
        }
        $id = $request->query->get('id');
        $count = $this->getDoctrine()->getRepository(Currency::class)->countCurrencies();
        if ($id >= 0 and $id <= $count)
            return $this->forward('App\Controller\DefaultController:jsonIdAction', array(
                'id'  => $id,
                'request' => $request,
                'paginator' => $paginator,
            ));
        else
            return $this->redirect('/', 301);
    }

    /**
     * @Route("/json/{id}", methods={"get"})
     */
    public function jsonIdAction($id, Request $request, PaginatorInterface $paginator): Response
    {
        /*
         * Формирование HTTP ответа в формате JSON по полученным параметрам в GET:
         * Диапазон дат от $start_date до $end_date с сортировкой $order
         * Для одной валюты $id или всех сразу $id=0
         */
        $start_date = \DateTime::createFromFormat('Y-m-d', $request->query->get('from'));
        $end_date = \DateTime::createFromFormat('Y-m-d', $request->query->get('to'));
        $doctrine = $this->getDoctrine();
        $order = $doctrine->getRepository(Currency::class)->getOrder($request->query->get('order'));
        if ($id == 0)
            $queryBuilder = $doctrine->getRepository(ExchangeRate::class)->findBetweenDatesAllCurrenciesPage($start_date, $end_date, $order);
        else
        {
            $currency = $doctrine->getRepository(Currency::class)->findOneBy(['id' => $id]);
            $queryBuilder = $doctrine->getRepository(ExchangeRate::class)->findBetweenDatesOneCurrencyPage($currency, $start_date, $end_date, $order);
        }

        $array = array();
        // Лимит отдачи в JSON за раз 1000 элементов
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            1000
        );
        foreach ($pagination as $rate)
        {
            // Выбранный формат данных
            $json = array (
                "currency"  => $rate->getIdCurrency()->getName(),
                "id_currency" => $rate->getIdCurrency()->getIdCurrency(),
                "nominal"   => $rate->getNominal(),
                "date" => $rate->getDate()->format('Y-m-d'),
                "value" => $rate->getValue()
            );
            array_push($array, $json);
        }
        $response = new JsonResponse($array);
        $response->setEncodingOptions( $response->getEncodingOptions() | JSON_PRETTY_PRINT );
        return $response;
    }
}
