<?php

namespace ServiceBundle\Services;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\EntityManager;
use ServiceBundle\Components\{
    Number, Utils, ViPdf
};
use ServiceBundle\Entity\OrderDocument;
use ServiceBundle\Services\Pdf\{Contract, Invoice, Inventory};
use Numbers_Words;

class PdfService extends MessageHandler
{
    const CURRENCY = 'ru';

    /**
     * @var EntityManager $em
     */
    private $_em;
    /**
     * @var ViPdf
     */
    private $_pdf;

    /**
     * @return EntityManager
     */
    public function getEm(): EntityManager
    {
        return $this->_em;
    }

    /**
     * @param EntityManager $em
     */
    public function setEm(EntityManager $em)
    {
        $this->_em = $em;
    }

    /**
     * @return ViPdf
     */
    public function getPdf(): ViPdf
    {
        return $this->_pdf;
    }

    /**
     * @param ViPdf $this->getPdf()
     */
    public function setPdf(ViPdf $pdf)
    {
        $pdf->setContainer($this);
        $this->_pdf = $pdf;
    }

    /**
     * PdfService constructor.
     *
     * @param string $orientation
     * @param string $unit
     * @param string $format
     * @param bool   $unicode
     * @param string $encoding
     * @param bool   $diskcache
     * @param bool   $pdfa
     */
    public function __construct(
        $orientation = 'P',
        $unit = 'mm',
        $format = 'A4',
        $unicode = true,
        $encoding = 'UTF-8',
        $diskcache = false,
        $pdfa = false
    ) {
        //$this->setEm($this->getDoctrine()->getManager());
        $this->setPdf(new ViPdf($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa));
    }

    /**
     * Возврат денег за заказ
     *
     * @param array $position
     */
    public function moneyReturn(array $position): void
    {
//        $position = [
//            'amount' => 1650000,
//            'order_id' => 45743,
//            'name' => 'Meizu M6 Note',
//        ];

        $this->getPdf()->startPageGroup();
        $this->getPdf()->AddPage();
        $margins = $this->getPdf()->GetMargins();
        $width = $this->getPdf()->GetPageWidth() - $margins['left'] - $margins['right'];
        $width2 = ($width / 2) + $margins['left'];
        $this->getPdf()->setXY($width2, $margins['top']);
        $this->getPdf()->Ln(15);

        $this->getPdf()->SetFont('dejavusanscondensed', 'B', 14);
        $title1 = 'Расписка';
        $title2 = 'в получении денежных средств';

        $this->getPdf()->Cell(0, 0, $title1, 0, 1, 'C');
        $this->getPdf()->Cell(0, 0, $title2, 0, 1, 'C');

        $this->getPdf()->SetTitle($title1 . ' ' . $title2);
        $this->getPdf()->Ln(2);

        $this->getPdf()->SetFont('dejavusanscondensed', '', 14);

        $this->getPdf()->PrintSignature('г.', 50, null, null, 10);
        $this->getPdf()->Cell(0, 0, '«____» ____________ 201 __ года.', 0, 1, 'R');

        $this->getPdf()->Ln(10);

        $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
        $this->getPdf()->Cell(0, 0,
            'Я, ___________________________________________________, «_____» _______________ _________ года рождения,',
            0, 1, 'C');

        $this->getPdf()->Ln(1);

        $this->getPdf()->SetFont('dejavuserif', 'I', 6);
        $this->getPdf()->Cell(80, 0, '(фамилия, имя, отчество полностью)', 0, 1, 'R');

        $this->getPdf()->Ln(1);

        $this->getPdf()->PrintSignature('проживающий по адресу: ', 185, null, null, 10);
        $this->getPdf()->Cell(0, 0, ' ,', 0, 1, 'R');

        $this->getPdf()->Ln(1);
        $this->getPdf()->Cell(0, 0, 'получил от: ', 0, 1, 'L');

        $this->getPdf()->Ln(1);
        $this->getPdf()->Cell(0, 0, 'ИП Мокиевская Н.Е. ОГРН ИП 315583500000191, г.Пенза, ул.Леонова 37-171', 0, 1,
            'L');

        $this->getPdf()->Ln(1);
        $this->getPdf()->Cell(0, 0, 'денежные средства в размере', 0, 1, 'L');

        $this->getPdf()->Ln(1);
        $this->getPdf()->Cell(0, 0, Number::format($position['amount'],
                0) . ' (' . Number::toStrShort($position['amount']) . ') рублей 00 коп.', 0, 1, 'L');

        $this->getPdf()->Ln(3);
        $this->getPdf()->Cell(0, 0,
            'Денежные средства получены за оплаченный мной по заказу №' . $position['order_id'] . ' товар', 0, 1, 'L');
        $this->getPdf()->Ln(3);

        $this->getPdf()->Cell(0, 0, $position['name'], 0, 1, 'L');
        $this->getPdf()->Ln(1);

        $this->getPdf()->Ln(10);
        $this->getPdf()->Cell(0, 0,
            '___________________  (____________________________________________________________)', 0, 1, 'R');

        $this->getPdf()->Ln(1);
        $this->getPdf()->SetFont('dejavuserif', 'I', 6);
        $this->getPdf()->Cell(230, 0,
            '(подпись)                                               (фамилия, имя, отчество полностью)', 0, 1, 'C');

        $this->getPdf()->Output('moneyReturn.pdf', 'I');
    }

    /**
     * Квитанция о предоплате
     *
     * @param array $invoices
     */
    public function prepayInvoice(array $invoices): void
    {
        foreach ($invoices as $invoice) {
            $this->_invoice($invoice);
        }

        $this->getPdf()->Output('prepayInvoice.pdf', 'I');
    }

    /**
     * Товарный чек
     *
     * @param array $receipts
     */
    public function payment(array $receipts): void
    {
        foreach ($receipts as $receipt) {
            $this->_receipt($receipt);
        }

//        if ($invoices = $this->_loadInvoices($this->request->id)) {
//            foreach ($invoices as $invoice) {
//                $this->_invoice($invoice);
//            }
//        }

        $this->getPdf()->Output('payment.pdf', 'I');
    }

    /**
     * Счет
     *
     * @param array $orderInvoice
     */
    public function invoice(array $orderInvoice)
    {
        $this->setPdf(new Invoice($orderInvoice));
        $this->getPdf()->SetProtection(['modify', 'copy', 'annot-forms', 'fill-forms', 'extract', 'assemble']);

        $this->_orderInvoice($orderInvoice);
        $this->_addOrderDocument($this->getParameter('pdf.documents.orders.invoices.path'), [
                'type' => OrderDocument::TYPE_INVOICE,
                'order_id' => $orderInvoice['order_id'],
                'number' => $orderInvoice['order_id'],
            ]
        );
    }

    /**
     * Договор
     */
    public function contract(array $orderContract)
    {
        $this->setPdf(new Contract($orderContract));
        $this->getPdf()->SetProtection(['modify', 'copy', 'annot-forms', 'fill-forms', 'extract', 'assemble']);

        $this->_orderContract($orderContract);
        $this->_addOrderDocument($this->getParameter('pdf.documents.orders.contracts.path'), [
            'type' => OrderDocument::TYPE_CONTRACT,
            'order_id' => $orderContract['order_id'],
            'number' => $orderContract['order_id'],
        ]);
    }

    /**
     * Коммерческое предложение
     *
     * @param array $orderCommercial
     */
    public function commercial(array $orderCommercial)
    {
        $this->setPdf(new Contract($orderCommercial));
        $this->getPdf()->SetProtection(['modify', 'copy', 'annot-forms', 'fill-forms', 'extract', 'assemble']);

        $this->_orderCommercial($orderCommercial);
        $this->getPdf()->IncludeJS('this.print({bUI:false,bSilent:true,bShrinkToFit:true});');
        $this->_addOrderDocument($this->getParameter('pdf.documents.orders.commercials.path'), [
            'type' => OrderDocument::TYPE_COMMERCIAL,
            'order_id' => $orderCommercial['order_id'],
            'number' => $orderCommercial['order_id'],
        ]);
    }

    /**
     * Инвентаризационная опись
     *
     * @param array  $data
     * @param string $filePath
     */
    public function inventory(array $data, $filePath) : void
    {
        $this->setPdf(new Inventory($data, 'L'));
        $this->getPdf()->SetProtection(['modify', 'copy', 'annot-forms', 'fill-forms', 'extract', 'assemble']);

        $this->_inventory($data);

        if (!file_exists($this->getParameter('pdf.documents.inventories.path'))) {
            mkdir($this->getParameter('pdf.documents.inventories.path'), 0777, true);
        }

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $this->getPdf()->Output($filePath, "FI");
    }

    /**
     * @param array $data
     *
     * @return int
     */
    private function _addOrderDocument(string $dir, array $data): int
    {
        $fileName = $dir . DIRECTORY_SEPARATOR . $this->_generateHash() . '.pdf';

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $this->getPdf()->Output($fileName, "FI");

        $currentUserId = null;
        /**
         * @var \AppBundle\Entity\User $user
         */
        $user = $this->get('user.identity')->getUser();
        if (!empty($user)) {
            $currentUserId = $user->getId();
        }

        $model = new OrderDocument();
        $model->setOrderId($data['order_id']);
        $model->setType($data['type']);
        $model->setNumber($data['number']);
        $model->setCreatedAt((new \DateTime()));
        $model->setCreatedBy($currentUserId);
        $model->setSentAt(null);
        $model->setContacts(json_encode([]));
        $model->setWithStamp(null);
        $model->setIsObsolete(null);
        $model->setUrl($fileName);
        $model->setPrepaymentPercent(null);
        $model->setContractType(null);

        $this->getEm()->persist($model);
        $this->getEm()->flush();

        return $model->getId();
    }

    private function _invoice(array $invoice)
    {
        $this->getPdf()->startPageGroup();
        $this->getPdf()->AddPage();
        $margins = $this->getPdf()->GetMargins();
        $width = $this->getPdf()->GetPageWidth() - $margins['left'] - $margins['right'];
        $width2 = ($width / 2) + $margins['left'];
        $this->getPdf()->PrintSeller($invoice, $width2);
        $y = $this->getPdf()->GetY();
        $this->getPdf()->PrintLogo($width2, $margins['top']);
        $this->getPdf()->SetY($y);
        $this->getPdf()->Ln(16);
        if ($invoice['type'] == 'invoice') {
            $text = 'Информацию о дате прихода вашего товара вы можете узнать на главной странице сайта ' . AbstractSender::SHOP_ADDRESS;
            $this->getPdf()->SetFont('dejavusans', '', 8);
            $this->getPdf()->SetCellPaddings(2, 1, 2, 1);
            $this->getPdf()->MultiCell($width, 0, $text, 1, 'C', false, 1, $this->getPdf()->GetX(),
                $this->getPdf()->GetY(), true, 0, true, true, 0, 'M', false);
            $this->getPdf()->Ln(1);
        }
        $this->getPdf()->SetCellPadding(0);
        $this->getPdf()->SetFont('dejavusans', '', 17);
        $title1 = 'Квитанция о внесении авансового платежа';
        $title2 = '№ СЧ-' . $invoice['id'] . ' от ' . date('d.m.Y', strtotime($invoice['datetime']));
        $this->getPdf()->SetTitle($title1 . ' ' . $title2);
        $this->getPdf()->Cell(0, 0, $title1, 0, 1, 'C');
        $this->getPdf()->Cell(0, 0, $title2, 0, 1, 'C');
        $this->getPdf()->Ln(2);
        $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
        $text = '(зак. ' . implode(', ', array_keys($invoice['orders'])) . ')';
        $this->getPdf()->MultiCell($width, 0, $text, 0, 'L', false, 1, $this->getPdf()->GetX(), $this->getPdf()->GetY(),
            true, 0, true, true, 0, 'M', false);
        $this->getPdf()->Ln(2);

        $this->_print($invoice);

        $this->getPdf()->SetCellPadding(0);
        if ($r = intval($invoice['amount'])) {
            $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
            list($r, $k) = explode('.', Number::format($r, 2));
            $this->getPdf()->Cell(0, 0, 'Авансовый платеж: ' . $r . ' руб. ' . $k . ' коп.', 0, 1);
        }
//            $oldInvoices = Model::admin('Invoice')->getOldByOrders(array_keys($invoice['orders']), !empty($invoice['active_outdated']));
//            if (!empty($oldInvoices)) {
//                $oldInvoiceIds = [];
//                foreach ($oldInvoices as $oldInvoice) {
//                    $oldInvoiceIds[] = 'СЧ-'.$oldInvoice['id'];
//                }
//                if (1 == count($oldInvoices)) {
//                    $this->getPdf()->MultiCell($width, 0, 'При внесении данного авансового платежа, предыдущию квитанцию за номером: '.implode(', ', $oldInvoiceIds).' считать не действительной.', 0, 'L', false, 1, $this->getPdf()->GetX(), $this->getPdf()->GetY(), true, 0, true, true, 0, 'M', false);
//                }
//                else {
//                    $this->getPdf()->MultiCell($width, 0, 'При внесении данного авансового платежа, все предыдущие квитанции за номерами: '.implode(', ', $oldInvoiceIds).' считать не действительными.', 0, 'L', false, 1, $this->getPdf()->GetX(), $this->getPdf()->GetY(), true, 0, true, true, 0, 'M', false);
//                }
//            }
        $this->getPdf()->Ln(2);
        $this->getPdf()->SetFont('dejavuserif', 'I', 6);
        $this->getPdf()->Write(1,
            'Оформляя настоящий предварительный заказ, Вы, как потребитель, соглашаетесь с публичной офертой ИП Мокиевская Н.Е. о предоставлении услуги по поставке указанного товара в соответствии со статьями 426, 435, 436, 437 Гражданского кодекса Российской Федерации. Согласно статье 429 Гражданского кодекса Российской Федерации (предварительный договор) в случае отказа от заявки ИП Мокиевская Н.Е. вправе требовать неустойки в размере затрат, понесенных в связи с доставкой товара, но не менее 20% от суммы заказа. Данные требования соответствуют содержанию статьи 438 Гражданского кодекса Российской Федерации (акцепт публичной оферты при оформлении заказа посредством сети «Интернет» либо непосредственно в офисе продаж). Цена действительна на момент оформления заказа. Возможна корректировка в пределах инфляции, установленной ЦБ РФ. Срок поставки товара составляет 45 рабочих дней со дня внесения авансового платежа. В случае невозможности осуществить поставку заказанного товара авансовый платеж подлежит возврату в течение трех рабочих дней.',
            '', false, 'L', true);
        $this->getPdf()->Ln(10);
        $this->getPdf()->PrintSignature('Продавец', 60);
        $this->getPdf()->PrintSignature('Покупатель', 60, $width2);

        /**
         * @TODO доделать вставку баннеров
         */

//            $banner = Model::factory('Banner')->getOneRandom('main', 0);
//            if (file_exists($this->getParametr('banner.images.path').'/image_'.$banner['id'].'.jpg')) {
//                $banner_html = '';
//                $this->getPdf()->SetXY(17, -70);
//                $this->getPdf()->setImageScale(2);
//                $this->getPdf()->Image($this->getParametr('banner.images.path').'/image_'.$banner['id'].'.jpg', $this->getPdf()->GetX(), $this->getPdf()->GetY(), 0, 0, 'JPG');
//
//                if($banner['title']) {
//                    $banner_html .= '<p><h2 style="font-size: 28px;">'.$banner['title'].'</h2></p>';
//                }
//
//                if($banner['text']) {
//                    $banner_html .= '<p style="font-size: 20px;">'.$banner['text'].'</p>';
//                }
//
//                if($banner['text2']) {
//                    if($banner['text2_RUR']) {
//                        $banner_html .= '<p style="font-size: 20px;"><b>'.$banner['text2'].' Р</b></p>';
//                    } else {
//                        $banner_html .= '<p style="font-size: 20px;"><b>'.$banner['text2'].'</b></p>';
//                    }
//                }
//                $banner_html = '<table><tr><td width="8%"></td><td width="35%">'.$banner_html.'</td></tr></table>';
//                $this->getPdf()->SetXY(10, -64);
//                $this->getPdf()->SetFont('dejavusanscondensed', '', 8);
//                $this->getPdf()->WriteHTML($banner_html, true, false, true, false, '');
//            }
    }

    private function _receipt(array $receipt)
    {
        $this->getPdf()->startPageGroup();
        $this->getPdf()->AddPage();
        $margins = $this->getPdf()->GetMargins();
        $width = $this->getPdf()->GetPageWidth() - $margins['left'] - $margins['right'];
        $this->getPdf()->PrintLogo();
        $width2 = ($width / 2) + $margins['left'];
        $this->getPdf()->setXY($width2, $margins['top']);
        $this->getPdf()->PrintSeller($receipt, $width2 - $margins['right'], 'R');
        $this->getPdf()->Ln(15);
        if ($receipt['payment'] == 'cashless') {
            $this->getPdf()->SetFont('dejavusans', '', 14);
            $title1 = 'Акт приема-передачи товара по заказу №' . $receipt['order_id'] . ' от ' . date('d.m.Y',
                    strtotime($receipt['datetime']));
            $title2 = '';
            $type = 'безналичный расчет';
        } elseif ('torg' == $receipt['point_type']) {
            $this->getPdf()->SetFont('dejavusans', '', 17);
            $title1 = 'ТОВАРНАЯ НАКЛАДНАЯ';
            $title2 = '№ ТН-' . $receipt['id'] . ' от ' . date('d.m.Y', strtotime($receipt['datetime']));
            $type = 'наличный расчет';
        } else {
            $this->getPdf()->SetFont('dejavusans', '', 17);
            $title1 = 'ТОВАРНЫЙ ЧЕК';
            $title2 = '№ СЧ-' . $receipt['id'] . ' от ' . date('d.m.Y', strtotime($receipt['datetime']));
            $type = 'розничная торговля';
        }
        $this->getPdf()->Cell(0, 0, $title1, 0, 1, 'C');
        if ($title2) {
            $this->getPdf()->Cell(0, 0, $title2, 0, 1, 'C');
        }
        $this->getPdf()->SetTitle($title1 . ' ' . $title2);
        $this->getPdf()->Ln(1);
        $this->getPdf()->SetFont('dejavuserif', 'I', 9);
        $this->getPdf()->Cell(0, 0, $type, 0, 1, 'C');
        $this->getPdf()->Ln(1);
        $this->getPdf()->SetFont('dejavusanscondensed', '', 9);
        if ($receipt['payment'] != 'cashless') {
            $text = '(зак. ' . implode(', ', array_keys($receipt['orders'])) . ')';
            $this->getPdf()->MultiCell($width, 0, $text, 0, 'L', false, 1, $this->getPdf()->GetX(),
                $this->getPdf()->GetY(), true, 0, true, true, 0, 'M', false);
            $this->getPdf()->Ln(1);
        }

        $this->_print($receipt);

        if ($receipt['payment'] != 'cashless' && 'torg' == $receipt['point_type']) {
            $this->getPdf()->Ln(2);
            $this->getPdf()->SetFont('dejavuserif', 'I', 7);
            $this->getPdf()->Write(1,
                'Товар приобретен при непосредственном ознакомлении. Механические повреждения отсутствуют.', '', false,
                'L', true);
            $this->getPdf()->Ln(10);
            $this->getPdf()->PrintSignature('Продавец', 60);
            $this->getPdf()->PrintSignature('Принял', 60, $width2);
            $this->getPdf()->Ln(20);

            $this->_paymentsTable();

            $this->getPdf()->Ln(10);
            $this->getPdf()->PrintSignature('Товар полностью оплачен', 60);
        } else {
            $this->getPdf()->Ln(2);
            $this->getPdf()->SetFont('dejavuserif', 'I', 7);
            $this->getPdf()->Write(1,
                'Товар приобретен' . ('credit' == $receipt['payment'] ? ' по розничному договору купли продажи в кредит с использованием банка,' : '') . ' при непосредственном ознакомлении. Комплектация проверена, механические повреждения отсутствуют. Проверка на работоспособность произведена. Наосновании Постановления Правительства Российской Федерации от 19 января 1998 г. No55 технически сложные товары надлежащего качества возврату и обмену не подлежат. В отношении остальных товаров возврат осуществляется в течение семи дней с даты покупки, на условии возмещения расходов на ' . ('credit' == $receipt['payment'] ? 'возврат' : 'доставку') . ' товара.',
                '', false, 'L', true);
            $this->getPdf()->Ln(1);
            $this->getPdf()->Write(1,
                'При обнаружении неисправности товара клиент обращается в сервисный центр производителя. На товары, не прошедшие сертификацию, распространяется гарантия от магазина и составляет 10 месяцев (на аксессуары - 6 месяцев) с момента покупки в соответствии с "Законом о защите прав потребителей". Таковыми могут быть товары, не подлежащие обязательной сертификации по постановлению Правительства Российской Федерации №982 от 1 декабря 2009г, они помечены знаком « * » в конце наименования. В данном случае гарантийное обслуживание осуществляет магазин, в котором была произведена покупка. На мебель так же предоставляется гарантия от магазина сроком 90 дней. Доставка товара в сервисный центр осуществляется силами и средствами клиента. На товар, имеющий технические повреждения, являющийся разукомплектованным, покупатель обязан предоставить акт технической экспертизы товара. Продавец не несёт ответственности и не возмещает возможные издержки клиента, связанные с ожиданием исполнения заказа.',
                '', false, 'L', true);
            $this->getPdf()->Ln(1);
            $this->getPdf()->Write(1, 'С потребительскими свойствами и характеристиками товара ознакомлен.', '', false,
                'L', true);
            $this->getPdf()->Ln(10);
            $this->getPdf()->PrintSignature('Продавец', 60);
            $this->getPdf()->PrintSignature('Покупатель', 60, $width2);
            $this->getPdf()->Ln(10);
            $this->getPdf()->PrintSignature('', 60);
        }
    }

    private function _paymentsTable()
    {
        $margins = $this->getPdf()->GetMargins();
        $widths = array(20, 0, 40);
        $widths[1] = $this->getPdf()->GetPageWidth() - $margins['left'] - $margins['right'] - array_sum($widths);
        $this->getPdf()->SetFont('dejavusanscondensed', 'I', 8);
        $this->getPdf()->SetCellPaddings(0, 0, 0, 0);
        $this->getPdf()->PrintTableHeadRow($widths, array('Дата', 'Оплачено, руб.', 'Принял денежные средства'));

        for ($i = 0; $i < 3; $i++) {
            $this->getPdf()->PrintTableBodyRow($widths, 10, [0 => '', 1 => '', 2 => '']);
        }
    }

    /**
     * @param array $invoice
     */
    private function _print(array $invoice)
    {
        $single = count($invoice['orders']) == 1;
        $margins = $this->getPdf()->GetMargins();
        $widths = array(8, 0, 16, 20, 25);
        if ($invoice['type'] == 'invoice' or $invoice['type'] == 'receipt' and $invoice['payment'] == 'cashless') {
            unset($widths[3], $widths[4]);
        }
        $widths[1] = $this->getPdf()->GetPageWidth() - $margins['left'] - $margins['right'] - array_sum($widths);
        $this->getPdf()->SetFont('dejavusanscondensed', 'I', 8);
        $this->getPdf()->SetCellPaddings(0.5, 0.5, 0.5, 0.5);
        $this->getPdf()->PrintTableHeadRow($widths, array('#', 'Наименование', 'Кол-во', 'Цена', 'Сумма'),
            ['R', 'L', 'C', 'R', 'R']);
        $align = array('R', 'L', 'C', 'R', 'R');
        $num = 0;
        $bottom = $this->getPdf()->GetPageHeight() - $margins['top'] - $this->getPdf()->GetStringHeight($widths[1],
                '0') * 5;
        $amount[$page = $this->getPdf()->getAliasNumPage()] = 0;
        foreach ($invoice['orders'] as $orderId => $positions) {
            if (!$single) {
                $this->getPdf()->SetFont('dejavusanscondensed', '', 7);
                $this->_calcHeight($widths[1], '0', $bottom, $amount[$page]);
                $this->getPdf()->Cell(0, 0, 'заказ ' . $orderId, 1, 0, 'L');
                $this->getPdf()->Ln();
            }
            $this->getPdf()->SetFont('dejavusanscondensed', 'I', 8);
            $this->getPdf()->SetCellPaddings(1, 0, 1, 0);
            foreach ($positions as $position) {
                if ($invoice['type'] == 'receipt') {
                    $position['price'] = round($position['amount'] / $position['quantity']);
                } else {
                    $position['amount'] = $position['price'] * $position['quantity'];
                }
                $amount[$page] += $position['amount'];
                if (!empty($position['warehouse']) && 'SOTM' == $position['warehouse']) {
                    $position['name'] .= ' *';
                }
                $data = array(
                    ++$num,
                    $position['name'],
                    $position['quantity'],
                    Number::format($position['price']),
                    Number::format($position['amount']),
                );
                $height = $this->_calcHeight($widths[1], $position['name'], $bottom, $amount[$page]);
                if ($invoice['type'] == 'receipt') {
                    $this->getPdf()->PrintTableBodyRow($widths, $height, $data, $align, 'LRT');
                    $data = array_fill(0, 5, '');
                    $data[1] = '';
                    if (isset($position['room_names']) and isset($position['room_reserves'])) {
                        $reserves = json_decode($position['room_reserves'], true);
                        foreach (json_decode($position['room_names'], true) as $roomId => $name) {
                            $data[1][] = $name . ': ' . $reserves[$roomId];
                        }

                        $data[1] = implode(', ', $data[1]);
                    }
                    $height = $this->_calcHeight($widths[1], $data[1], $bottom, $amount[$page]);
                    $this->getPdf()->setColor('text', 127);
                    $this->getPdf()->SetFont('dejavusanscondensed', 'I', 6);
                    $this->getPdf()->PrintTableBodyRow($widths, $height, $data, [1 => 'R'] + $align, 'LRB');
                    $this->getPdf()->setXY($margins['left'] + $widths[0], $this->getPdf()->getY() - $height);
                    $this->getPdf()->Cell($widths[1], $height,
                        'код ' . $position['base_product_id'] . ($position['arriving_date'] ? ' - ' . $position['warehouse'] . ' ' . date('d.m',
                                strtotime($position['arriving_date'])) : ''));
                    $this->getPdf()->Ln();
                    $this->getPdf()->setColor('text', 0);
                    $this->getPdf()->SetFont('dejavusanscondensed', 'I', 8);
                } else {
                    $this->getPdf()->PrintTableBodyRow($widths, $height, $data, $align);
                }
            }
        }
        if ($invoice['type'] == 'invoice' or $invoice['type'] == 'receipt' and $invoice['payment'] == 'cashless') {
            $this->getPdf()->ln(3);
        } else {
            $amount = array_sum($amount);
            $this->getPdf()->Cell(0, 0, 'Итого: ' . Number::format($amount), 1, 1, 'R');
            $this->getPdf()->Ln(2);
            $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
            $this->getPdf()->SetCellPadding(0);
            $this->getPdf()->MultiCell(0, 0, 'Сумма: ' . Number::toStr($amount), 0, 'L');
        }
    }

    /**
     * @param       $width
     * @param       $text
     * @param       $bottom
     * @param       $amount
     *
     * @return float
     */
    private function _calcHeight($width, $text, $bottom, $amount)
    {
        $height = $this->getPdf()->GetStringHeight($width, $text);
        if ($this->getPdf()->getY() + $height > $bottom) {
            $this->getPdf()->Cell(0, 0, 'Итого по странице: ' . Number::format($amount), 1, 1, 'R');
            $margins = $this->getPdf()->GetMargins();
            $width = $this->getPdf()->GetPageWidth() - $margins['left'] - $margins['right'];
            $width2 = $width / 2 + $margins['left'];
            $this->getPdf()->PrintSignature('Продавец', 60);
            $this->getPdf()->PrintSignature('Покупатель', 60, $width2);
            $this->getPdf()->AddPage();
        }

        return $height;
    }

    /**
     * @param array         $widths
     * @param string        $text
     * @param               $bottom
     * @param int           $pageIndex
     * @param int           $pageInitialQuantity
     * @param int           $pageInitialSum
     * @param int           $pageFoundQuantity
     * @param int           $pageFoundSum
     * @param Numbers_Words $numbersWords
     * @param int           $totalIndex
     * @param int           $totalCount
     *
     * @return float
     */
    private function _calcInvoiceHeight(array $widths, string $text, $bottom, int &$pageIndex, int &$pageInitialQuantity, int &$pageInitialSum, int &$pageFoundQuantity, int &$pageFoundSum, Numbers_Words $numbersWords, int $totalIndex, int $totalCount)
    {
        $height = $this->getPdf()->GetStringHeight($widths[1], $text);

        if ($this->getPdf()->getY() + $height > $bottom || ($totalIndex == $totalCount)) {
            $this->getPdf()->SetFont('dejavusanscondensed', '', 8);
            $this->getPdf()->PrintTableBodyRow(
                $widths,
                6,
                ['', '', '', 'Итого', $pageInitialQuantity, $pageInitialSum, $pageFoundQuantity, $pageFoundSum,],
                ['C', 'L', 'C', 'C', 'C', 'C', 'C', 'C',]
            );

            $this->getPdf()->Ln(5);
            $this->getPdf()->SetFont('dejavusanscondensed', '', 9);
            $this->getPdf()->WriteHTML('Итого по странице:', true, false, true, false, 'L');

            $this->getPdf()->Ln(2);

            $this->getPdf()->MultiCell(0, 0, 'а) количество порядковых номеров', 0, 'L', false, 0, 40, '', true, 0, false, true);
            $this->getPdf()->PrintUnderSignature('(прописью)', 170, 110, $this->getPdf()->GetY(), 6, $numbersWords->toWords($pageIndex));
            $this->getPdf()->Ln(4);

            $this->getPdf()->SetFont('dejavusanscondensed', '', 9);
            $this->getPdf()->MultiCell(0, 0, 'б) общее количество единиц фактически', 0, 'L', false, 0, 40, '', true, 0, false, true);
            $this->getPdf()->PrintUnderSignature('(прописью)', 170, 110, $this->getPdf()->GetY(), 6, $numbersWords->toWords($pageInitialQuantity));
            $this->getPdf()->Ln(4);

            $this->getPdf()->SetFont('dejavusanscondensed', '', 9);
            $this->getPdf()->MultiCell(0, 0, 'в) на сумму фактически', 0, 'L', false, 0, 40, '', true, 0, false, true);
            $this->getPdf()->PrintUnderSignature('(прописью)', 170, 110, $this->getPdf()->GetY(), 6, $numbersWords->toCurrency($pageInitialSum, self::CURRENCY));

            $pageIndex = $pageInitialQuantity = $pageInitialSum = $pageFoundQuantity = $pageFoundSum = 0;

            if ($totalIndex != $totalCount) {
                // new page
                $this->getPdf()->AddPage();
                $this->getPdf()->Ln(5);
            }
        }

        return $height;
    }

    /**
     * @param array $orderInvoice
     */
    private function _orderInvoice(array $orderInvoice)
    {
        $this->getPdf()->startPageGroup();
        $this->getPdf()->AddPage();
        $this->getPdf()->SetFont('dejavusanscondensed', '', 11);
        $this->getPdf()->WriteHTML($orderInvoice['sections'], true, false, true, false, '');
        $this->getPdf()->Ln(6);
        $curr_y = $this->getPdf()->GetY();
        $total_y = $this->getPdf()->GetPageHeight();
        if ($total_y - $curr_y < 60) {
            $this->getPdf()->AddPage();
        }
    }

    /**
     * @param array $orderContract
     */
    private function _orderContract(array $orderContract)
    {
        $this->getPdf()->startPageGroup();
        $this->getPdf()->AddPage();
        $this->getPdf()->SetFont('dejavusanscondensed', '', 11);
        $this->getPdf()->WriteHTML($orderContract['sections'], true, false, true, false, '');
        $this->getPdf()->Ln(3);

        $this->getPdf()->AddPage();
        $this->getPdf()->Ln(5);
        $this->getPdf()->WriteHTML($orderContract['requisites'], true, false, true, false, '');
        $this->getPdf()->Ln(6);

        if (!empty($orderContract['with_stamp'])) {
            if ($orderContract['seller_tin'] === $this->getPdf()::SOKOLOV_TIN) {
                $this->getPdf()->setImageScale(5.5);
                $this->getPdf()->SetXY(45, 139);
                $this->getPdf()->Image($this->getParameter('project.web.images.path') . '/document/' . $this->getPdf()::SOKOLOV_TIN . '_signature.png',
                    $this->getPdf()->GetX(), $this->getPdf()->GetY(), 0, 0, 'PNG');
                $this->getPdf()->SetXY(10, 110);
                $this->getPdf()->setImageScale(3.5);
                $this->getPdf()->Image($this->getParameter('project.web.images.path') . '/document/' . $this->getPdf()::SOKOLOV_TIN . '_stamp.png',
                    $this->getPdf()->GetX(), $this->getPdf()->GetY(), 0, 0, 'PNG');
            } else {
                $this->getPdf()->setImageScale(4.5);
                $this->getPdf()->SetXY(45, 140);
                $this->getPdf()->Image($this->getParameter('project.web.images.path') . '/document/' . $this->getPdf()::MOKIEVSKAYA_TIN . '_signature.png',
                    $this->getPdf()->GetX(), $this->getPdf()->GetY(), 0, 0, 'PNG');
                $this->getPdf()->SetXY(10, 110);
                $this->getPdf()->setImageScale(3.5);
                $this->getPdf()->Image($this->getParameter('project.web.images.path') . '/document/' . $this->getPdf()::MOKIEVSKAYA_TIN . '_stamp.png',
                    $this->getPdf()->GetX(), $this->getPdf()->GetY(), 0, 0, 'PNG');
            }
        }

        if ($orderContract['contract_type'] === OrderDocument::CONTRACT_TYPE_RETAIL) {
            $this->getPdf()->AddPage();
            $this->getPdf()->Ln(3);
            $this->getPdf()->WriteHTML($orderContract['specification'], true, false, true, false, '');
            $this->getPdf()->Ln(5);
        }
    }

    /**
     * @param array $orderCommercial
     */
    private function _orderCommercial(array $orderCommercial)
    {
        $this->getPdf()->startPageGroup();
        $this->getPdf()->AddPage();
        $this->getPdf()->SetFont('dejavusanscondensed', '', 11);
        $this->getPdf()->WriteHTML($orderCommercial['sections'], true, false, true, false, '');
        $this->getPdf()->Ln(6);
    }

    /**
     * @param array $inventory
     */
    private function _inventory(array $inventory)
    {
        $numbersWords = new Numbers_Words();
        $numbersWords->locale = self::CURRENCY;

        $this->getPdf()->startPageGroup();

        ////// PAGE 1
        $this->getPdf()->AddPage();
        $this->getPdf()->Ln(10);

        $this->getPdf()->PrintUnderSignature('(организация)', 200, 50, $this->getPdf()->GetY(), 6, $inventory['org_name']);
        $this->getPdf()->Ln(7);
        $this->getPdf()->PrintUnderSignature('(структурное подразделение)', 200, 50, $this->getPdf()->GetY(), 6, $inventory['org_unit']);
        $this->getPdf()->Ln(7);

        $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
        $this->getPdf()->WriteHTML('Дата начала инвентаризации: '.$inventory['created_at'], true, false, true, true, 'R');
        $this->getPdf()->Ln(2);
        $this->getPdf()->WriteHTML('Дата окончания инвентаризации: '.$inventory['completed_at'], true, false, true, false, 'R');

        $this->getPdf()->Ln(5);

        $this->getPdf()->SetX($this->getPdf()->getPageWidth() - 105);
        $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
        $widths = [50, 50,];
        $this->getPdf()->SetCellPaddings(0, 0, 0, 0);
        $this->getPdf()->PrintTableHeadRow($widths, array('Номер документа', 'Дата составления',), ['C', 'C',]);

        $this->getPdf()->SetX($this->getPdf()->getPageWidth() - 105);
        for ($i = 0; $i < 1; $i++) {
            $this->getPdf()->PrintTableBodyRow($widths, 7, [0 => $inventory['number'], 1 => $inventory['created_at'],], ['C', 'C',]);
        }

        $this->getPdf()->SetFont('dejavusanscondensed', '', 13);
        $this->getPdf()->Write(10, 'ИНВЕНТАРИЗАЦИОННАЯ ОПИСЬ', '', false, 'C');
        $this->getPdf()->Ln();

        $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
        $this->getPdf()->Write(10, 'товаров', '', false, 'C');

        $this->getPdf()->Ln(15);

        $this->getPdf()->SetXY($this->getPdf()->GetX(), $this->getPdf()->GetY());

        $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
        $this->getPdf()->MultiCell(0, 0, 'Местонахождение', 0, 'L', false, 0, 5, '', true, 0, false, true);
        $this->getPdf()->PrintUnderSignature('', 250, 40, $this->getPdf()->GetY(), 6, $inventory['location']);

        $this->getPdf()->Ln(10);

        $this->getPdf()->SetFont('dejavusanscondensed', '', 13);
        $this->getPdf()->Write(10, 'РАСПИСКА', '', false, 'C');

        $this->getPdf()->Ln(10);

        $this->getPdf()->SetFont('dejavusanscondensed', '', 9);
        $this->getPdf()->WriteHTML('К началу проведения инвентаризации все расходные и приходные документы на товары сданы в бухгалтерию, и все', true, false, true, false, 'C');
        $this->getPdf()->WriteHTML('товары, поступившие на мою (нашу) ответственность, оприходованы, а выбывшие списаны в расход.', true, false, true, false, 'C');

        $this->getPdf()->Ln(10);

        $this->getPdf()->MultiCell(0, 0, 'Лицо(а), ответственное(ые) за сохранность товаров:', 0, 'L', false, 0, 50, '', true, 0, false, true);

        foreach ($inventory['responsiblePeoples'] as $responsiblePeople) {
            $y = $this->getPdf()->GetY();
            $this->getPdf()->PrintUnderSignature('(должность)', 40, 150, $y, 6, $responsiblePeople['position']);
            $this->getPdf()->PrintUnderSignature('(подпись)', 40, 200, $y);
            $this->getPdf()->PrintUnderSignature('(расшифровка подписи)', 40, 250, $y, 6, $responsiblePeople['name']);

            $this->getPdf()->Ln(5);
        }

        ////// PAGE 2
        $this->getPdf()->AddPage();
        $this->getPdf()->Ln(5);

        $margins = $this->getPdf()->GetMargins();
        $this->getPdf()->SetFont('dejavusanscondensed', '', 8);
        $widths = [7, 0, 25, 25, 25, 25, 30, 30,];
        $widths[1] = $this->getPdf()->GetPageWidth() - $margins['left'] - $margins['right'] - array_sum($widths);

        $this->getPdf()->SetCellPaddings(1, 0, 0, 0);
        $this->getPdf()->PrintTableHeadRow(
            $widths,
            [
                '#',
                'Наименование товара',
                'Код товара',
                'Штрихкод',
                'Факт. наличие, кол-во',
                'Факт. наличие, стоимость',
                'По данным учета, кол-во',
                'По данным учета, стоимость',
            ],
            ['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C',]
        );

        $index = 1;
        $totalInitialQuantity = $totalInitialSum = 0;
        $pageIndex = $pageInitialQuantity = $pageInitialSum = $pageFoundQuantity = $pageFoundSum = 0;
        $bottom = $this->getPdf()->GetPageHeight() - $margins['top'] - 55;
        foreach ($inventory['items'] as $item) {
            $this->getPdf()->SetFont('dejavusanscondensed', '', 8);
            $this->getPdf()->PrintTableBodyRow(
                $widths,
                5,
                [
                    $index,
                    $item['name'],
                    $item['code'],
                    $item['barcode'],
                    $item['initial_quantity'],
                    $item['initial_sum'],
                    $item['found_quantity'],
                    $item['found_sum'],
                ],
                ['C', 'L', 'C', 'C', 'C', 'C', 'C', 'C',]
            );

            $this->_calcInvoiceHeight($widths, '0', $bottom, $pageIndex, $pageInitialQuantity, $pageInitialSum, $pageFoundQuantity, $pageFoundSum, $numbersWords, $index, count($inventory['items']));

            $pageIndex++;
            $pageInitialQuantity += $item['initial_quantity'];
            $pageInitialSum += $item['initial_sum'];
            $pageFoundQuantity += $item['found_quantity'];
            $pageFoundSum += $item['found_sum'];

            $index++;
            $totalInitialQuantity += $item['initial_quantity'];
            $totalInitialSum += $item['initial_sum'];
        }

        ////// PAGE 3
        $index--;
        $this->getPdf()->AddPage();
        $this->getPdf()->Ln(10);

        $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
        $this->getPdf()->MultiCell(0, 0, 'Итого по описи:', 0, 'L', false, 0, 5, '', true, 0, false, true);

        $this->getPdf()->MultiCell(0, 0, 'а) количество порядковых номеров', 0, 'L', false, 0, 35, '', true, 0, false, true);
        $this->getPdf()->PrintUnderSignature('(прописью)', 185, 105, $this->getPdf()->GetY(), 6, number_format($index, 0, '.', ' ') . ' ('.$numbersWords->toWords($index).')');
        $this->getPdf()->Ln(5);

        $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
        $this->getPdf()->MultiCell(0, 0, 'б) общее количество единиц фактически', 0, 'L', false, 0, 35, '', true, 0, false, true);
        $this->getPdf()->PrintUnderSignature('(прописью)', 185, 105, $this->getPdf()->GetY(), 6, number_format($totalInitialQuantity, 0, '.', ' ').' ('.$numbersWords->toWords($totalInitialQuantity).')');
        $this->getPdf()->Ln(5);

        $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
        $this->getPdf()->MultiCell(0, 0, 'в) на сумму фактически', 0, 'L', false, 0, 35, '', true, 0, false, true);
        $this->getPdf()->PrintUnderSignature('(прописью)', 185, 105, $this->getPdf()->GetY(), 6, number_format($totalInitialSum, 2, '.', ' ').' ('.$numbersWords->toCurrency($totalInitialSum, self::CURRENCY).')');
        $this->getPdf()->Ln(5);

        $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
        $this->getPdf()->WriteHTML('Все подсчеты итогов по строкам, страницам и в целом по инвентаризационной описи товаров проверены.', true, false, true, true, 'L');

        $this->getPdf()->Ln(3);

        $this->getPdf()->MultiCell(0, 0, 'Председатель комиссии', 0, 'L', false, 0, 5, '', true, 0, false, true);
        $y = $this->getPdf()->GetY();
        $this->getPdf()->PrintUnderSignature('(должность)', 60, 50, $y, 6, $inventory['chairman']['position']);
        $this->getPdf()->PrintUnderSignature('(подпись)', 60, 120, $y);
        $this->getPdf()->PrintUnderSignature('(расшифровка подписи)', 60, 190, $y, 6, $inventory['chairman']['name']);

        $this->getPdf()->Ln(5);

        $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
        $this->getPdf()->MultiCell(0, 0, 'Члены комиссии:', 0, 'L', false, 0, 5, '', true, 0, false, true);
        foreach ($inventory['members'] as $responsiblePeople) {
            $y = $this->getPdf()->GetY();
            $this->getPdf()->PrintUnderSignature('(должность)', 60, 50, $y, 6, $responsiblePeople['position']);
            $this->getPdf()->PrintUnderSignature('(подпись)', 60, 120, $y);
            $this->getPdf()->PrintUnderSignature('(расшифровка подписи)', 60, 190, $y, 6, $responsiblePeople['name']);

            $this->getPdf()->Ln(5);
        }

        $this->getPdf()->Ln(5);

        $y = $this->getPdf()->GetY();
        $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
        $this->getPdf()->MultiCell(0, 0, 'Все товары, поименованные в настоящей инвентаризационной описи с №', 0, 'L', false, 0, 5, '', true, 0, false, true);
        $this->getPdf()->PrintUnderSignature('', 10, 135, $y, 6, '1');
        $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
        $this->getPdf()->MultiCell(0, 0, 'по №', 0, 'L', false, 0, 145, $y, true, 0, false, true);
        $this->getPdf()->PrintUnderSignature('', 10, 155, $y, 6, $index);
        $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
        $this->getPdf()->MultiCell(0, 0, ', комиссией', 0, 'L', false, 0, 165, $y, true, 0, false, true);

        $this->getPdf()->Ln(7);

        $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
        $this->getPdf()->WriteHTML('проверены в натуре в моем (нашем) присутствии и внесены в опись, в связи с чем претензий к инвентаризационной комиссии не имею (не имеем).', true, false, true, true, 'L');
        $this->getPdf()->Ln();
        $this->getPdf()->WriteHTML('Товары, перечисленные в описи, находятся на моем (нашем) ответственном хранении.', true, false, true, true, 'L');
        $this->getPdf()->Ln(5);

        $this->getPdf()->MultiCell(0, 0, 'Лицо(а), ответственное(ые) за сохранность товаров:', 0, 'L', false, 0, 5, '', true, 0, false, true);

        foreach ($inventory['responsiblePeoples'] as $responsiblePeople) {
            $y = $this->getPdf()->GetY();
            $this->getPdf()->PrintUnderSignature('(должность)', 40, 150, $y, 6, $responsiblePeople['position']);
            $this->getPdf()->PrintUnderSignature('(подпись)', 40, 200, $y);
            $this->getPdf()->PrintUnderSignature('(расшифровка подписи)', 40, 250, $y, 6, $responsiblePeople['name']);

            $this->getPdf()->Ln(5);
        }

        $this->_printDateForm();

        $this->getPdf()->Ln(20);

        $this->getPdf()->MultiCell(0, 0, 'Указанные в настоящей описи данные и расчеты проверил', 0, 'L', false, 0, 5, '', true, 0, false, true);
        $y = $this->getPdf()->GetY();
        $this->getPdf()->PrintUnderSignature('(должность)', 40, 150, $y, 6, $inventory['checker']['position']);
        $this->getPdf()->PrintUnderSignature('(подпись)', 40, 200, $y);
        $this->getPdf()->PrintUnderSignature('(расшифровка подписи)', 40, 250, $y, 6, $inventory['checker']['name']);

        $this->getPdf()->Ln(5);

        $this->_printDateForm();
    }

    /**
     * @return string
     */
    private function _generateHash() : string
    {
        return sha1(microtime(true).rand(1, 1000));
    }

    private function _printDateForm() : void
    {
        $y = $this->getPdf()->GetY();
        $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
        $this->getPdf()->MultiCell(0, 0, '«', 0, 'L', false, 0, 150, '', true, 0, false, true);
        $this->getPdf()->PrintUnderSignature('', 10, 155, $y, 6, '');
        $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
        $this->getPdf()->MultiCell(0, 0, '»', 0, 'L', false, 0, 165, $y, true, 0, false, true);
        $this->getPdf()->PrintUnderSignature('', 40, 170, $y, 6, '');
        $this->getPdf()->PrintUnderSignature('', 20, 215, $y, 6, '');
        $this->getPdf()->SetFont('dejavusanscondensed', '', 10);
        $this->getPdf()->MultiCell(0, 0, 'г.', 0, 'L', false, 0, 238, $y, true, 0, false, true);

    }
}