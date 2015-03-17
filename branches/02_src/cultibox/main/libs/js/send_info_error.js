<script type="text/javascript">
$(document).ready(function(){
    <?php
    // If there are some informations, display them
    if((isset($main_info))&&(!empty($main_info))&&(count($main_info)>0)) {
        // Add every element given by array main_info
        foreach($main_info as $key => $value) { 
            echo "pop_up_add_information(\"" . $value . "\", \"" . $key . "\", \"information\");";
        }
    }
    
    // If there are some errors, display them
    if((isset($main_error))&&(!empty($main_error))&&(count($main_error)>0)) {
        // Add every element given by array main_error
        foreach($main_error as $key => $value) { 
            echo "pop_up_add_information(\"" . $value . "\", \"" . $key . "\", \"error\");";      
        }
    }
    ?>
});
</script>
