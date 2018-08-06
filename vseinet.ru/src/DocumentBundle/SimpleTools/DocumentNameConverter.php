<?php

namespace DocumentBundle\SimpleTools;

class DocumentNameConverter
{

    static $documentsList = [
    //  'table_name' => 'url_section',
        'supply_doc' => 'supplies',
        'order_doc' => 'orders',
        'goods_acceptance_doc' => 'goodsAcceptances',
        'goods_packaging' => 'goodsPackagings',
        'goods_issue_doc' => 'goodsIssues',
        'goods_release_doc' => 'goodsReleases',
        'inventory' => 'inventories',
        'goods_decision_doc' => 'goodsDecisions',
        'financial_operation_doc' => 'simpleFinancialOperations',
        'bank_operation_doc' => 'bankOperations',
        'expense_simple_doc' => 'simpleExpenses',
        'accountable_expenses_doc' => 'accountableExpenses',
        'supplier_order_expenses_doc' => 'supplierOrderExpenses',
        'buyer_order_expenses_doc' => 'buyerOrderExpenses',
    ];

    static public function Type2TableName($strType)
    {
        return array_search($strType, DocumentNameConverter::$documentsList, true);
    }

    static public function TableName2Type($strTableName)
    {
        if (empty(DocumentNameConverter::$documentsList[$strTableName])) {
            return false;
        }else{
            return DocumentNameConverter::$documentsList[$strTableName];
        }
    }

}