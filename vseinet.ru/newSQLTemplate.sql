
        with
            cte_expense_simple_doc as (
                select
        -- общие поля документов

        qexpense_simple_doc.did as id,
        qexpense_simple_doc.number as number,
        case 
            when qexpense_simple_doc.parent_doc_did is null then null
            else
                json_build_object(
                    'id', qexpense_simple_doc_parent.did, 
                    'title', qexpense_simple_doc_parent.title, 
                    'document_type', qexpense_simple_doc_parent_class.relname)
            end as parent_doc,
        qexpense_simple_doc.created_at as created_at,
        qexpense_simple_doc.created_by as created_by,
        case
            when qexpense_simple_doc.created_by is not null then
                concat(
                    case when qexpense_simple_doc_person_created.firstname is null then '' else qexpense_simple_doc_person_created.firstname end,
                    case when qexpense_simple_doc_person_created.secondname is null and qexpense_simple_doc_person_created.lastname is null then '' else ' ' end,
                    case when qexpense_simple_doc_person_created.secondname is null then '' else qexpense_simple_doc_person_created.secondname end,
                    case when qexpense_simple_doc_person_created.lastname is null then '' else ' ' end,
                    case when qexpense_simple_doc_person_created.lastname is null then '' else qexpense_simple_doc_person_created.lastname end)
            else null end as created_name,
        qexpense_simple_doc.completed_at as completed_at,
        qexpense_simple_doc.completed_by as completed_by,
        case
            when qexpense_simple_doc.completed_by is not null then
                concat(
                    case when qexpense_simple_doc_person_completed.firstname is null then '' else qexpense_simple_doc_person_completed.firstname end,
                    case when qexpense_simple_doc_person_completed.secondname is null and qexpense_simple_doc_person_completed.lastname is null then '' else ' ' end,
                    case when qexpense_simple_doc_person_completed.secondname is null then '' else qexpense_simple_doc_person_completed.secondname end,
                    case when qexpense_simple_doc_person_completed.lastname is null then '' else ' ' end,
                    case when qexpense_simple_doc_person_completed.lastname is null then '' else qexpense_simple_doc_person_completed.lastname end)
            else null end as completed_name,
        qexpense_simple_doc.registered_at as registered_at,
        qexpense_simple_doc.registered_by as registered_by,
        case
            when qexpense_simple_doc.registered_by is not null then
                concat(
                    case when qexpense_simple_doc_person_registered.firstname is null then '' else qexpense_simple_doc_person_registered.firstname end,
                    case when qexpense_simple_doc_person_registered.secondname is null and qexpense_simple_doc_person_registered.lastname is null then '' else ' ' end,
                    case when qexpense_simple_doc_person_registered.secondname is null then '' else qexpense_simple_doc_person_registered.secondname end,
                    case when qexpense_simple_doc_person_registered.lastname is null then '' else ' ' end,
                    case when qexpense_simple_doc_person_registered.lastname is null then '' else qexpense_simple_doc_person_registered.lastname end)
            else null end as registered_name,
        qexpense_simple_doc.title as title,
        qexpense_simple_doc.status_code as status_code,
                    -- общие поля расходов
                    'expense_simple_doc' as document_type,
                    qexpense_simple_doc.org_department_id,
                    qexpense_simple_doc.amount,
                    qexpense_simple_doc.expected_date_execute,
                    qexpense_simple_doc.description,
                    qexpense_simple_doc.accepted_at,
                    qexpense_simple_doc.accepted_by,
                    qexpense_simple_doc.rejected_at,
                    qexpense_simple_doc.rejected_by,
                    qexpense_simple_doc.expected_date_execute as expected_date_execute,
                    -- expense_simple_doc
                    qexpense_simple_doc.financial_resource_id,
                    qexpense_simple_doc.item_of_expenses_id,
                    qexpense_simple_doc.equipment_id,
                    null as          financial_counteragent_id,
                    null as          to_item_of_expenses_id,
                    null as          to_equipment_id,
                    null as          maturity_date_execute,
                    null as          amount_bonus,
                    null as          amount_mutual,
                    null as          relative_documents_ids,
                    null as          to_financial_resource_id
                    
                from "expense_simple_doc" qexpense_simple_doc 
        -- документ-основние
        left join any_doc* qexpense_simple_doc_parent on qexpense_simple_doc_parent.did = qexpense_simple_doc.parent_doc_did
        left join pg_class qexpense_simple_doc_parent_class on qexpense_simple_doc_parent_class.oid = qexpense_simple_doc_parent.tableoid

        -- автор документа
        left join "user" qexpense_simple_doc_user_created on qexpense_simple_doc_user_created.id = qexpense_simple_doc.created_by
        left join "person" qexpense_simple_doc_person_created on qexpense_simple_doc_person_created.id = qexpense_simple_doc_user_created.person_id

        -- кто закрыл документ
        left join "user" qexpense_simple_doc_user_completed on qexpense_simple_doc_user_completed.id = qexpense_simple_doc.completed_by
        left join "person" qexpense_simple_doc_person_completed on qexpense_simple_doc_person_completed.id = qexpense_simple_doc_user_completed.person_id

        -- кто провёл документ
        left join "user" qexpense_simple_doc_user_registered on qexpense_simple_doc_user_registered.id = qexpense_simple_doc.registered_by
        left join "person" qexpense_simple_doc_person_registered on qexpense_simple_doc_person_registered.id = qexpense_simple_doc_user_registered.person_id
                where true
                    -- общие фильтры
            
                    -- wgere to expense_simple_doc
                order by qexpense_simple_doc.expected_date_execute desc, qexpense_simple_doc.create_at desc, qexpense_simple_doc.did
            limit 50 offset 0
                ),
            cte_accountable_expenses_doc as (
                select
        -- общие поля документов

        qaccountable_expenses_doc.did as id,
        qaccountable_expenses_doc.number as number,
        case 
            when qaccountable_expenses_doc.parent_doc_did is null then null
            else
                json_build_object(
                    'id', qaccountable_expenses_doc_parent.did, 
                    'title', qaccountable_expenses_doc_parent.title, 
                    'document_type', qaccountable_expenses_doc_parent_class.relname)
            end as parent_doc,
        qaccountable_expenses_doc.created_at as created_at,
        qaccountable_expenses_doc.created_by as created_by,
        case
            when qaccountable_expenses_doc.created_by is not null then
                concat(
                    case when qaccountable_expenses_doc_person_created.firstname is null then '' else qaccountable_expenses_doc_person_created.firstname end,
                    case when qaccountable_expenses_doc_person_created.secondname is null and qaccountable_expenses_doc_person_created.lastname is null then '' else ' ' end,
                    case when qaccountable_expenses_doc_person_created.secondname is null then '' else qaccountable_expenses_doc_person_created.secondname end,
                    case when qaccountable_expenses_doc_person_created.lastname is null then '' else ' ' end,
                    case when qaccountable_expenses_doc_person_created.lastname is null then '' else qaccountable_expenses_doc_person_created.lastname end)
            else null end as created_name,
        qaccountable_expenses_doc.completed_at as completed_at,
        qaccountable_expenses_doc.completed_by as completed_by,
        case
            when qaccountable_expenses_doc.completed_by is not null then
                concat(
                    case when qaccountable_expenses_doc_person_completed.firstname is null then '' else qaccountable_expenses_doc_person_completed.firstname end,
                    case when qaccountable_expenses_doc_person_completed.secondname is null and qaccountable_expenses_doc_person_completed.lastname is null then '' else ' ' end,
                    case when qaccountable_expenses_doc_person_completed.secondname is null then '' else qaccountable_expenses_doc_person_completed.secondname end,
                    case when qaccountable_expenses_doc_person_completed.lastname is null then '' else ' ' end,
                    case when qaccountable_expenses_doc_person_completed.lastname is null then '' else qaccountable_expenses_doc_person_completed.lastname end)
            else null end as completed_name,
        qaccountable_expenses_doc.registered_at as registered_at,
        qaccountable_expenses_doc.registered_by as registered_by,
        case
            when qaccountable_expenses_doc.registered_by is not null then
                concat(
                    case when qaccountable_expenses_doc_person_registered.firstname is null then '' else qaccountable_expenses_doc_person_registered.firstname end,
                    case when qaccountable_expenses_doc_person_registered.secondname is null and qaccountable_expenses_doc_person_registered.lastname is null then '' else ' ' end,
                    case when qaccountable_expenses_doc_person_registered.secondname is null then '' else qaccountable_expenses_doc_person_registered.secondname end,
                    case when qaccountable_expenses_doc_person_registered.lastname is null then '' else ' ' end,
                    case when qaccountable_expenses_doc_person_registered.lastname is null then '' else qaccountable_expenses_doc_person_registered.lastname end)
            else null end as registered_name,
        qaccountable_expenses_doc.title as title,
        qaccountable_expenses_doc.status_code as status_code,
                    -- общие поля расходов
                    'accountable_expenses_doc' as document_type,
                    qaccountable_expenses_doc.org_department_id,
                    qaccountable_expenses_doc.amount,
                    qaccountable_expenses_doc.expected_date_execute,
                    qaccountable_expenses_doc.description,
                    qaccountable_expenses_doc.accepted_at,
                    qaccountable_expenses_doc.accepted_by,
                    qaccountable_expenses_doc.rejected_at,
                    qaccountable_expenses_doc.rejected_by,
                    qaccountable_expenses_doc.expected_date_execute as expected_date_execute,
                    -- accountable_expenses_doc
                    qaccountable_expenses_doc.financial_resource_id,
                    null as          item_of_expenses_id,
                    null as          equipment_id,
                    qaccountable_expenses_doc.financial_counteragent_id,
                    qaccountable_expenses_doc.to_item_of_expenses_id,
                    qaccountable_expenses_doc.to_equipment_id,
                    qaccountable_expenses_doc.maturity_date_execute,
                    null as          amount_bonus,
                    null as          amount_mutual,
                    null as          relative_documents_ids,
                    null as          to_financial_resource_id
                    
                from "accountable_expenses_doc" qaccountable_expenses_doc 
        -- документ-основние
        left join any_doc* qaccountable_expenses_doc_parent on qaccountable_expenses_doc_parent.did = qaccountable_expenses_doc.parent_doc_did
        left join pg_class qaccountable_expenses_doc_parent_class on qaccountable_expenses_doc_parent_class.oid = qaccountable_expenses_doc_parent.tableoid

        -- автор документа
        left join "user" qaccountable_expenses_doc_user_created on qaccountable_expenses_doc_user_created.id = qaccountable_expenses_doc.created_by
        left join "person" qaccountable_expenses_doc_person_created on qaccountable_expenses_doc_person_created.id = qaccountable_expenses_doc_user_created.person_id

        -- кто закрыл документ
        left join "user" qaccountable_expenses_doc_user_completed on qaccountable_expenses_doc_user_completed.id = qaccountable_expenses_doc.completed_by
        left join "person" qaccountable_expenses_doc_person_completed on qaccountable_expenses_doc_person_completed.id = qaccountable_expenses_doc_user_completed.person_id

        -- кто провёл документ
        left join "user" qaccountable_expenses_doc_user_registered on qaccountable_expenses_doc_user_registered.id = qaccountable_expenses_doc.registered_by
        left join "person" qaccountable_expenses_doc_person_registered on qaccountable_expenses_doc_person_registered.id = qaccountable_expenses_doc_user_registered.person_id
                where true
                    -- общие фильтры
            
                    -- where to accountable_expenses_doc
                order by qaccountable_expenses_doc.expected_date_execute desc, qaccountable_expenses_doc.create_at desc, qaccountable_expenses_doc.did
            limit 50 offset 0
                ),
            cte_supplier_order_expenses_doc as (
                select
        -- общие поля документов

        qsupplier_order_expenses_doc.did as id,
        qsupplier_order_expenses_doc.number as number,
        case 
            when qsupplier_order_expenses_doc.parent_doc_did is null then null
            else
                json_build_object(
                    'id', qsupplier_order_expenses_doc_parent.did, 
                    'title', qsupplier_order_expenses_doc_parent.title, 
                    'document_type', qsupplier_order_expenses_doc_parent_class.relname)
            end as parent_doc,
        qsupplier_order_expenses_doc.created_at as created_at,
        qsupplier_order_expenses_doc.created_by as created_by,
        case
            when qsupplier_order_expenses_doc.created_by is not null then
                concat(
                    case when qsupplier_order_expenses_doc_person_created.firstname is null then '' else qsupplier_order_expenses_doc_person_created.firstname end,
                    case when qsupplier_order_expenses_doc_person_created.secondname is null and qsupplier_order_expenses_doc_person_created.lastname is null then '' else ' ' end,
                    case when qsupplier_order_expenses_doc_person_created.secondname is null then '' else qsupplier_order_expenses_doc_person_created.secondname end,
                    case when qsupplier_order_expenses_doc_person_created.lastname is null then '' else ' ' end,
                    case when qsupplier_order_expenses_doc_person_created.lastname is null then '' else qsupplier_order_expenses_doc_person_created.lastname end)
            else null end as created_name,
        qsupplier_order_expenses_doc.completed_at as completed_at,
        qsupplier_order_expenses_doc.completed_by as completed_by,
        case
            when qsupplier_order_expenses_doc.completed_by is not null then
                concat(
                    case when qsupplier_order_expenses_doc_person_completed.firstname is null then '' else qsupplier_order_expenses_doc_person_completed.firstname end,
                    case when qsupplier_order_expenses_doc_person_completed.secondname is null and qsupplier_order_expenses_doc_person_completed.lastname is null then '' else ' ' end,
                    case when qsupplier_order_expenses_doc_person_completed.secondname is null then '' else qsupplier_order_expenses_doc_person_completed.secondname end,
                    case when qsupplier_order_expenses_doc_person_completed.lastname is null then '' else ' ' end,
                    case when qsupplier_order_expenses_doc_person_completed.lastname is null then '' else qsupplier_order_expenses_doc_person_completed.lastname end)
            else null end as completed_name,
        qsupplier_order_expenses_doc.registered_at as registered_at,
        qsupplier_order_expenses_doc.registered_by as registered_by,
        case
            when qsupplier_order_expenses_doc.registered_by is not null then
                concat(
                    case when qsupplier_order_expenses_doc_person_registered.firstname is null then '' else qsupplier_order_expenses_doc_person_registered.firstname end,
                    case when qsupplier_order_expenses_doc_person_registered.secondname is null and qsupplier_order_expenses_doc_person_registered.lastname is null then '' else ' ' end,
                    case when qsupplier_order_expenses_doc_person_registered.secondname is null then '' else qsupplier_order_expenses_doc_person_registered.secondname end,
                    case when qsupplier_order_expenses_doc_person_registered.lastname is null then '' else ' ' end,
                    case when qsupplier_order_expenses_doc_person_registered.lastname is null then '' else qsupplier_order_expenses_doc_person_registered.lastname end)
            else null end as registered_name,
        qsupplier_order_expenses_doc.title as title,
        qsupplier_order_expenses_doc.status_code as status_code,
                    -- общие поля расходов
                    'supplier_order_expenses_doc' as document_type,
                    qsupplier_order_expenses_doc.org_department_id,
                    qsupplier_order_expenses_doc.amount,
                    qsupplier_order_expenses_doc.expected_date_execute,
                    qsupplier_order_expenses_doc.description,
                    qsupplier_order_expenses_doc.accepted_at,
                    qsupplier_order_expenses_doc.accepted_by,
                    qsupplier_order_expenses_doc.rejected_at,
                    qsupplier_order_expenses_doc.rejected_by,
                    qsupplier_order_expenses_doc.expected_date_execute as expected_date_execute,
                    -- supplier_order_expenses_doc
                    qsupplier_order_expenses_doc.financial_resource_id,
                    qsupplier_order_expenses_doc.item_of_expenses_id,
                    null as          equipment_id,
                    qsupplier_order_expenses_doc.financial_counteragent_id,
                    null as          to_item_of_expenses_id,
                    null as          to_equipment_id,
                    null as          maturity_date_execute,
                    qsupplier_order_expenses_doc.amount_bonus,
                    qsupplier_order_expenses_doc.amount_mutual,
                    qsupplier_order_expenses_doc.relative_documents_ids,
                    null as          to_financial_resource_id
                    
                from "supplier_order_expenses_doc" qsupplier_order_expenses_doc 
        -- документ-основние
        left join any_doc* qsupplier_order_expenses_doc_parent on qsupplier_order_expenses_doc_parent.did = qsupplier_order_expenses_doc.parent_doc_did
        left join pg_class qsupplier_order_expenses_doc_parent_class on qsupplier_order_expenses_doc_parent_class.oid = qsupplier_order_expenses_doc_parent.tableoid

        -- автор документа
        left join "user" qsupplier_order_expenses_doc_user_created on qsupplier_order_expenses_doc_user_created.id = qsupplier_order_expenses_doc.created_by
        left join "person" qsupplier_order_expenses_doc_person_created on qsupplier_order_expenses_doc_person_created.id = qsupplier_order_expenses_doc_user_created.person_id

        -- кто закрыл документ
        left join "user" qsupplier_order_expenses_doc_user_completed on qsupplier_order_expenses_doc_user_completed.id = qsupplier_order_expenses_doc.completed_by
        left join "person" qsupplier_order_expenses_doc_person_completed on qsupplier_order_expenses_doc_person_completed.id = qsupplier_order_expenses_doc_user_completed.person_id

        -- кто провёл документ
        left join "user" qsupplier_order_expenses_doc_user_registered on qsupplier_order_expenses_doc_user_registered.id = qsupplier_order_expenses_doc.registered_by
        left join "person" qsupplier_order_expenses_doc_person_registered on qsupplier_order_expenses_doc_person_registered.id = qsupplier_order_expenses_doc_user_registered.person_id
                where true
                    -- общие фильтры
            
                    -- where to supplier_order_expenses_doc
                order by qsupplier_order_expenses_doc.expected_date_execute desc, qsupplier_order_expenses_doc.create_at desc, qsupplier_order_expenses_doc.did
            limit 50 offset 0
                ),
            cte_buyer_order_expenses_doc as (
                select
        -- общие поля документов

        qbuyer_order_expenses_doc.did as id,
        qbuyer_order_expenses_doc.number as number,
        case 
            when qbuyer_order_expenses_doc.parent_doc_did is null then null
            else
                json_build_object(
                    'id', qbuyer_order_expenses_doc_parent.did, 
                    'title', qbuyer_order_expenses_doc_parent.title, 
                    'document_type', qbuyer_order_expenses_doc_parent_class.relname)
            end as parent_doc,
        qbuyer_order_expenses_doc.created_at as created_at,
        qbuyer_order_expenses_doc.created_by as created_by,
        case
            when qbuyer_order_expenses_doc.created_by is not null then
                concat(
                    case when qbuyer_order_expenses_doc_person_created.firstname is null then '' else qbuyer_order_expenses_doc_person_created.firstname end,
                    case when qbuyer_order_expenses_doc_person_created.secondname is null and qbuyer_order_expenses_doc_person_created.lastname is null then '' else ' ' end,
                    case when qbuyer_order_expenses_doc_person_created.secondname is null then '' else qbuyer_order_expenses_doc_person_created.secondname end,
                    case when qbuyer_order_expenses_doc_person_created.lastname is null then '' else ' ' end,
                    case when qbuyer_order_expenses_doc_person_created.lastname is null then '' else qbuyer_order_expenses_doc_person_created.lastname end)
            else null end as created_name,
        qbuyer_order_expenses_doc.completed_at as completed_at,
        qbuyer_order_expenses_doc.completed_by as completed_by,
        case
            when qbuyer_order_expenses_doc.completed_by is not null then
                concat(
                    case when qbuyer_order_expenses_doc_person_completed.firstname is null then '' else qbuyer_order_expenses_doc_person_completed.firstname end,
                    case when qbuyer_order_expenses_doc_person_completed.secondname is null and qbuyer_order_expenses_doc_person_completed.lastname is null then '' else ' ' end,
                    case when qbuyer_order_expenses_doc_person_completed.secondname is null then '' else qbuyer_order_expenses_doc_person_completed.secondname end,
                    case when qbuyer_order_expenses_doc_person_completed.lastname is null then '' else ' ' end,
                    case when qbuyer_order_expenses_doc_person_completed.lastname is null then '' else qbuyer_order_expenses_doc_person_completed.lastname end)
            else null end as completed_name,
        qbuyer_order_expenses_doc.registered_at as registered_at,
        qbuyer_order_expenses_doc.registered_by as registered_by,
        case
            when qbuyer_order_expenses_doc.registered_by is not null then
                concat(
                    case when qbuyer_order_expenses_doc_person_registered.firstname is null then '' else qbuyer_order_expenses_doc_person_registered.firstname end,
                    case when qbuyer_order_expenses_doc_person_registered.secondname is null and qbuyer_order_expenses_doc_person_registered.lastname is null then '' else ' ' end,
                    case when qbuyer_order_expenses_doc_person_registered.secondname is null then '' else qbuyer_order_expenses_doc_person_registered.secondname end,
                    case when qbuyer_order_expenses_doc_person_registered.lastname is null then '' else ' ' end,
                    case when qbuyer_order_expenses_doc_person_registered.lastname is null then '' else qbuyer_order_expenses_doc_person_registered.lastname end)
            else null end as registered_name,
        qbuyer_order_expenses_doc.title as title,
        qbuyer_order_expenses_doc.status_code as status_code,
                    -- общие поля расходов
                    'buyer_order_expenses_doc' as document_type,
                    qbuyer_order_expenses_doc.org_department_id,
                    qbuyer_order_expenses_doc.amount,
                    qbuyer_order_expenses_doc.expected_date_execute,
                    qbuyer_order_expenses_doc.description,
                    qbuyer_order_expenses_doc.accepted_at,
                    qbuyer_order_expenses_doc.accepted_by,
                    qbuyer_order_expenses_doc.rejected_at,
                    qbuyer_order_expenses_doc.rejected_by,
                    qbuyer_order_expenses_doc.expected_date_execute as expected_date_execute,
                    -- buyer_order_expenses_doc
                    null as          financial_resource_id,
                    null as          item_of_expenses_id,
                    null as          equipment_id,
                    qbuyer_order_expenses_doc.financial_counteragent_id,
                    qbuyer_order_expenses_doc.to_item_of_expenses_id,
                    null as          to_equipment_id,
                    qbuyer_order_expenses_doc.maturity_date_execute,
                    null as          amount_bonus,
                    null as          amount_mutual,
                    null as          relative_documents_ids,
                    qbuyer_order_expenses_doc.to_financial_resource_id
                    
                from "buyer_order_expenses_doc" qbuyer_order_expenses_doc 
        -- документ-основние
        left join any_doc* qbuyer_order_expenses_doc_parent on qbuyer_order_expenses_doc_parent.did = qbuyer_order_expenses_doc.parent_doc_did
        left join pg_class qbuyer_order_expenses_doc_parent_class on qbuyer_order_expenses_doc_parent_class.oid = qbuyer_order_expenses_doc_parent.tableoid

        -- автор документа
        left join "user" qbuyer_order_expenses_doc_user_created on qbuyer_order_expenses_doc_user_created.id = qbuyer_order_expenses_doc.created_by
        left join "person" qbuyer_order_expenses_doc_person_created on qbuyer_order_expenses_doc_person_created.id = qbuyer_order_expenses_doc_user_created.person_id

        -- кто закрыл документ
        left join "user" qbuyer_order_expenses_doc_user_completed on qbuyer_order_expenses_doc_user_completed.id = qbuyer_order_expenses_doc.completed_by
        left join "person" qbuyer_order_expenses_doc_person_completed on qbuyer_order_expenses_doc_person_completed.id = qbuyer_order_expenses_doc_user_completed.person_id

        -- кто провёл документ
        left join "user" qbuyer_order_expenses_doc_user_registered on qbuyer_order_expenses_doc_user_registered.id = qbuyer_order_expenses_doc.registered_by
        left join "person" qbuyer_order_expenses_doc_person_registered on qbuyer_order_expenses_doc_person_registered.id = qbuyer_order_expenses_doc_user_registered.person_id
                where true
                    -- общие фильтры
            
                    -- where to buyer_order_expenses_doc
                order by qbuyer_order_expenses_doc.expected_date_execute desc, qbuyer_order_expenses_doc.create_at desc, qbuyer_order_expenses_doc.did
            limit 50 offset 0
                )