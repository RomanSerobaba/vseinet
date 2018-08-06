<?php 

namespace SupplyBundle\Bus\Reserves\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class DownloadCommand extends Message
{
    const FORMAT_CSV = 'csv';
    const FORMAT_EXCEL = 'xls';
    const FORMAT_STRING = 'txt';
    const FORMAT_HTML = 'html';

    const EXPORT_HTML_CODE = 'export_html_code';
    const EXPORT_HTML_NAME = 'export_html_name';
    const EXPORT_HTML_QUANTITY = 'export_html_quantity';
    const EXPORT_HTML_PRICE = 'export_html_price';

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Формат выгрузки")
     * @Assert\NotBlank(message="Value of 'format' should not be blank")
     * @Assert\Choice({"csv", "xls", "txt", "html"}, strict=true)
     * @VIA\DefaultValue("xls")
     */
    public $format;

    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @VIA\Description("Поставщик")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Точка")
     * @VIA\DefaultValue("0")
     */
    public $pointId;

    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("Экспорт кода")
     */
    public $isExportCode;

    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("Экспорт наименования")
     */
    public $isExportName;

    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("Экспорт количества")
     */
    public $isExportQuantity;

    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("Экспорт цены")
     */
    public $isExportPrice;

    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("С подтвержденными резервами")
     */
    public $withConfirmedReserves;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Название файла (не передавать)")
     */
    public $fileName;
}