<?php



} elseif((isset($import))&&(!empty($import))) {
    $target_path = "tmp/".basename($_FILES['upload_file']['name']);
    if(!move_uploaded_file($_FILES['upload_file']['tmp_name'], $target_path)) {
        $main_error[]=__('ERROR_UPLOADED_FILE');
        $pop_up_error_message=$pop_up_error_message.popup_message(__('ERROR_UPLOADED_FILE'));
    } else {
        $chprog=true;
        $data_prog=array();
        $data_prog=generate_program_from_file("$target_path",$import_selected,$main_error);
        if(count($data_prog)==0) {
            $main_error[]=__('ERROR_GENERATE_PROGRAM_FROM_FILE');
            $pop_up_error_message=$pop_up_error_message.popup_message(__('ERROR_GENERATE_PROGRAM_FROM_FILE'));
        } else {
            clean_program($import_selected,$program_index,$main_error);
            program\export_program($import_selected,$program_index,$main_error);

            if(!insert_program($data_prog,$main_error,$program_index))
                $chprog=false;

            if(!$chprog) {
                $main_error[]=__('ERROR_GENERATE_PROGRAM_FROM_FILE');
                $pop_up_error_message=$pop_up_error_message.popup_message(__('ERROR_GENERATE_PROGRAM_FROM_FILE'));

                $data_prog=generate_program_from_file("tmp/program_plug${import_selected}.prg",$import_selected,$main_error);

                if(!insert_program($data_prog,$main_error,$program_index))
                    $chprog=false;
            } else {
                $main_info[]=__('VALID_IMPORT_PROGRAM');
                $pop_up_message=$pop_up_message.popup_message(__('VALID_IMPORT_PROGRAM'));
            }
        }
    }
    $selected_plug=$import_selected;
    $reset_selected=$import_selected;
    $export_selected=$import_selected;
}


?>
