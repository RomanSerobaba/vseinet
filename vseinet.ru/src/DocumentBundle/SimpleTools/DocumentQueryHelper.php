<?php

namespace DocumentBundle\SimpleTools;

class DocumentQueryHelper
{

    const selectTemplate = "
        -- общие поля документов

        [tableAlias].did as id,
        [tableAlias].number as number,
        case
            when [tableAlias].parent_doc_did is null then null::json
            else
                json_build_object(
                    'id', [tableAlias]_parent.did,
                    'title', [tableAlias]_parent.title,
                    'document_type', [tableAlias]_parent_class.relname)
            end as parent_doc,
        [tableAlias].created_at as created_at,
        [tableAlias].created_by as created_by,
        case
            when [tableAlias].created_by is not null then
                concat(
                    case when [tableAlias]_person_created.firstname is null then '' else [tableAlias]_person_created.firstname end,
                    case when [tableAlias]_person_created.firstname is not null and [tableAlias]_person_created.secondname is not null then ' '
                        when [tableAlias]_person_created.firstname is not null and [tableAlias]_person_created.lastname is not null then ' '
                        else '' end,
                    case when [tableAlias]_person_created.secondname is null then '' else [tableAlias]_person_created.secondname end,
                    case when [tableAlias]_person_created.secondname is not null and [tableAlias]_person_created.lastname is not null then ' ' else '' end,
                    case when [tableAlias]_person_created.lastname is null then '' else [tableAlias]_person_created.lastname end)::text
            else null::text end as created_name,
        [tableAlias].completed_at as completed_at,
        [tableAlias].completed_by as completed_by,
        case
            when [tableAlias].completed_by is not null then
                concat(
                    case when [tableAlias]_person_completed.firstname is null then '' else [tableAlias]_person_completed.firstname end,
                    case when [tableAlias]_person_completed.firstname is not null and [tableAlias]_person_completed.secondname is not null then ' '
                        when [tableAlias]_person_completed.firstname is not null and [tableAlias]_person_completed.lastname is not null then ' '
                        else '' end,
                    case when [tableAlias]_person_completed.secondname is null then '' else [tableAlias]_person_completed.secondname end,
                    case when [tableAlias]_person_completed.secondname is not null and [tableAlias]_person_completed.lastname is not null then ' ' else '' end,
                    case when [tableAlias]_person_completed.lastname is null then '' else [tableAlias]_person_completed.lastname end)::text
            else null::text end as completed_name,
        [tableAlias].registered_at as registered_at,
        [tableAlias].registered_by as registered_by,
        case
            when [tableAlias].registered_by is not null then
                concat(
                    case when [tableAlias]_person_registered.firstname is null then '' else [tableAlias]_person_registered.firstname end,
                    case when [tableAlias]_person_registered.firstname is not null and [tableAlias]_person_registered.secondname is not null then ' '
                        when [tableAlias]_person_registered.firstname is not null and [tableAlias]_person_registered.lastname is not null then ' '
                        else '' end,
                    case when [tableAlias]_person_registered.secondname is null then '' else [tableAlias]_person_registered.secondname end,
                    case when [tableAlias]_person_registered.secondname is not null and [tableAlias]_person_registered.lastname is not null then ' ' else '' end,
                    case when [tableAlias]_person_registered.lastname is null then '' else [tableAlias]_person_registered.lastname end)::text
            else null::text end as registered_name,
        [tableAlias].title as title,
        [tableAlias].status_code as status_code";
    const joinTemplate = "
        -- документ-основние
        left join any_doc* [tableAlias]_parent on [tableAlias]_parent.did = [tableAlias].parent_doc_did
        left join pg_class [tableAlias]_parent_class on [tableAlias]_parent_class.oid = [tableAlias]_parent.tableoid

        -- автор документа
        left join \"user\" [tableAlias]_user_created on [tableAlias]_user_created.id = [tableAlias].created_by
        left join \"person\" [tableAlias]_person_created on [tableAlias]_person_created.id = [tableAlias]_user_created.person_id

        -- кто закрыл документ
        left join \"user\" [tableAlias]_user_completed on [tableAlias]_user_completed.id = [tableAlias].completed_by
        left join \"person\" [tableAlias]_person_completed on [tableAlias]_person_completed.id = [tableAlias]_user_completed.person_id

        -- кто провёл документ
        left join \"user\" [tableAlias]_user_registered on [tableAlias]_user_registered.id = [tableAlias].registered_by
        left join \"person\" [tableAlias]_person_registered on [tableAlias]_person_registered.id = [tableAlias]_user_registered.person_id";

    static public function buildSelect(string $tableAlias)
    {
        return str_replace('[tableAlias]', $tableAlias, DocumentQueryHelper::selectTemplate);
    }

    static public function buildJoin(string $tableAlias)
    {
        return str_replace('[tableAlias]', $tableAlias, DocumentQueryHelper::joinTemplate);
    }

    static public function personFullName(string $tableAlias)
    {
        return "
            case
                when {$tableAlias}.firstname is null and {$tableAlias}.secondname is null and {$tableAlias}.lastname is null then null::text
                else concat(
                    case when {$tableAlias}.firstname is null then '' else {$tableAlias}.firstname end,
                    case when {$tableAlias}.firstname is not null and {$tableAlias}.secondname is not null then ' '
                        when {$tableAlias}.firstname is not null and {$tableAlias}.lastname is not null then ' '
                        else '' end,
                    case when {$tableAlias}.secondname is null then '' else {$tableAlias}.secondname end,
                    case when {$tableAlias}.secondname is not null and {$tableAlias}.lastname is not null then ' ' else '' end,
                    case when {$tableAlias}.lastname is null then '' else {$tableAlias}.lastname end)::text
                end";
    }

}
