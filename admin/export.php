<?php
if (!defined('ABSPATH')) {
    exit('Direct\'s not allowed');
}
add_action('cf7d_after_bulkaction_btn', 'cf7d_after_bulkaction_btn_export_cb', 10);
function cf7d_after_bulkaction_btn_export_cb($fid)
{
    ?>
    <select id="cf7d-export" name="cf7d-export" data-fid="<?php echo $fid; ?>">
        <option value="-1"><?php _e('Export to...'); ?></option>
        <option value="csv"><?php _e('CSV'); ?></option>
    </select>
    <button class="button action" type="submit" name="btn_export"><?php _e('Export'); ?></button>

    <button class="button action" type="submit" name="btn_export_pdf" formtarget="_blank"><?php _e('PDF'); ?></button>
    <?php
}
add_action('cf7d_main_post', 'cf7d_export_action_cb');
function cf7d_export_action_cb()
{
    if (isset($_GET['cf7d-export']) && isset($_GET['btn_export'])) {
        add_filter('cf7d_get_current_action', false);
        $fid = (int)$_GET['fid'];

        $ids_export = ((isset($_GET['del_id'])) ? implode(',', $_GET['del_id']) : '');

        $type = $_GET['cf7d-export'];
        switch ($type) {
            case 'csv':
                cf7d_export_to_csv($fid, $ids_export);
                break;
            case '-1':
                return;
                break;
            default:
                return;
                break;
        }

    }
}

function cf7d_export_to_pdf_get_data_for_fid_36(){}
function cf7d_export_to_pdf_get_data_for_fid_(){}




//function trnsorm_pdf_data ($fid, $ids_exports = ''){
//
//    if (isset($_GET['cf7d-export']) && isset($_GET['btn_export_pdf'])) {
//        $fid = 36;
//        $data = cf7d_export_to_pdf_get_data($fid);
//        var_dump($data);
//
//    }
//
//
//}




add_action('cf7d_main_post', 'cf7d_export_action_pdf_cb');
function cf7d_export_action_pdf_cb()
{
    if (isset($_GET['cf7d-export']) && isset($_GET['btn_export_pdf'])) {
        $MAX_PDF_WIDTH = 400;
        // 1. data
        $fid = $_GET['fid'];
        $filter_ids = empty($_GET['del_id']) ? '' : implode(',', $_GET['del_id']);
        $columns = cf7d_get_db_fields($fid);
        $data = cf7d_export_to_pdf_get_data($fid, $filter_ids);

        $config = PdfConfig::call($fid);

        if (is_null($config)){
            die('pdf export forbidden');
        }

        $data = PdfTransformData::call($fid, $columns, $data);
        $header = PdfHeader::call($fid, $columns);

        // 2. widths
        $columnsCount = count($header);
        $headerWidths = [];
        for($i = 0; $i < $columnsCount; $i++){
            $headerWidths[] = $MAX_PDF_WIDTH/$columnsCount;
        }

        // 3. pdf config
        $pdf = new PDF('L', 'mm', 'A3' );
        $pdf->SetFont('Arial', '', 8);
        $pdf->AddPage();
        $pdf->SetWidths($headerWidths);

        // 4. fill data
        $pdf->Row($header);
        foreach($data as $row) {
            $pdf->Row($row);
        }

        $pdf->Output();
        exit();
    }
}
function cf7d_export_to_csv($fid, $ids_export = '')
{
    global $wpdb;

    $fields = cf7d_get_db_fields($fid);

    $data = cf7d_get_entrys($fid, $ids_export, 'data_id desc');
    $data_sorted = cf7d_sortdata($data);

    header("Content-type: text/x-csv");
    header("Content-Disposition: attachment; filename=cf7-database.csv");
    $fp = fopen('php://output', 'w');
    fputs($fp, "\xEF\xBB\xBF");
    fputcsv($fp, array_values($fields));
    foreach ($data_sorted as $k => $v) {
        $temp_value = array();
        foreach ($fields as $k2 => $v2) {
            $temp_value[] = ((isset($v[$k2])) ? $v[$k2] : '');
        }
        fputcsv($fp, $temp_value);
    }

    fclose($fp);
    exit();
}


function cf7d_export_to_pdf_get_data($fid, $ids_export = '')
{
    global $wpdb;
//    $ids_export = '';
//    $fid = 36;
    $fields = cf7d_get_db_fields($fid);

    $data = cf7d_get_entrys($fid, $ids_export, 'data_id desc');
    $data_sorted = cf7d_sortdata($data);

    return $data_sorted;

    header("Content-type: text/x-pdf");
    header("Content-Disposition: attachment; filename=cf7-database.pdf");
    $fp = fopen('php://output', 'w');
    fputs($fp, "\xEF\xBB\xBF");
    fputcsv($fp, array_values($fields));
    foreach ($data_sorted as $k => $v) {
        $temp_value = array();
        foreach ($fields as $k2 => $v2) {
            $temp_value[] = ((isset($v[$k2])) ? $v[$k2] : '');
        }
        fputcsv($fp, $temp_value);
    }

    fclose($fp);
    exit();
}
