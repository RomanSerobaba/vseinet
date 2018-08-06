<?php 

namespace AppBundle\Enum;

class GoodsDecisionDocType
{
    
/**    
 * Решение по товару - Вернуть на склад
 */
const GOODS_TO_WAREHOUSE = 'goods_to_warehouse';

/**    
 * Решение по товару - Вернуть клиенту
 */
const GOODS_TO_CLIENT = 'goods_to_client';

/**    
 * Решение по товару - Списать с остатков
 */
const GOODS_WRITE_OUT = 'goods_write_out';

/**    
 * Решение по клиенту - Вернуть деньги
 */
const CLIENT_MONEY_BACK = 'client_money_back';

/**    
 * Решение по клиенту - Вернуть товар
 */
const CLIENT_GOODS_BACK = 'client_goods_back';

/**    
 * Расчет с поставщиком - Возврат товара
 */
const SUPPLIER_GOODS_BACK = 'supplier_goods_back';

/**    
 * Расчет с поставщиком - Зачисление средств
 */
const SUPPLIER_MONEY_BACK = 'supplier_money_back';

/**    
 * Расчет с поставщиком - Отказ от претензий
 */
const SUPPLIER_WAIVER = 'supplier_waiver';

/**    
 * Расчет с поставщиком - Ожидает решения
 */
const SUPPLIER_IN_PROCESS = 'supplier_in_process';

}