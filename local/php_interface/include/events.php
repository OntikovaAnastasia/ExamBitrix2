<?

/*[ex2-51] Изменение данных в письме */
AddEventHandler("main", "OnBeforeEventAdd", "OnBeforeEventAddHandler");
function OnBeforeEventAddHandler(&$event, &$lid, &$arFields){
    if($event=='FEEDBACK_FORM'){
        global $USER;
        if($userId = $USER->getId()){
            $rsUser = CUser::GetByID($userId);
            $arUser = $rsUser->Fetch();

            $arFields['AUTHOR'] =str_replace(
                ['#ID#', '#LOGIN#', '#NAME#'],
                [$userId, $arUser['LOGIN'], $arUser['LAST_NAME'].' '.$arUser['NAME'].' '.$arUser['SECOND_NAME']],
                GetMessage("AUTH")
            ).$arFields['AUTHOR'];
        } else {
            $arFields['AUTHOR'] = GetMessage('NOT_AUTH'). $arFields['AUTHOR'];
        }
        CEventLog::Add(array(
            "SEVERITY" => "INFO",
            "AUDIT_TYPE_ID" => "FEEDBACK_FORM",
            "MODULE_ID" => "main",
            "DESCRIPTION" => GetMessage('FEEDBACK_DESC').$arFields['AUTHOR'],
        ));
    }
}
