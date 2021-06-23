<?php

if(!class_exists('WP_List_table')){
    require_once (ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

class ImitManageAnswers extends WP_List_Table{
    private $_items;
    function __construct($data)
    {
        parent::__construct();
        $this->_items = $data;
    }

    function get_columns()
    {
        return [
            'cb' => '<input type="checkbox" />',
            'user_id' => __('Name', 'imit-recozilla'),
            'post_id' => __('Post title', 'imit-recozilla'),
            'status' => __('Status', 'imit-recozilla'),
            'action' => __('Action', 'imit-recozilla'),
        ];
    }

    /**
     * @param array|object $item
     * @return string|void
     *
     * for check box
     */
    function column_cb($item){
        return "<input type='checkbox' value='{$item['id']}' />";
    }

    /**
     * @param array|object $item
     * @param string $column_name
     * @return mixed|void
     */
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    function column_user_id($item){
        $user_data = get_userdata($item['user_id']);
        if(!empty($user_data->user_firstname) && !empty($user_data->user_lastname)){
            return $user_data->user_firstname.' '.$user_data->user_lastname;
        }else{
            return $user_data->display_name;
        }
    }

    /**
     * get post title
     */
    function column_post_id($item){
        $get_post = get_post($item['post_id']);

        return wp_trim_words( $get_post->post_title, 5, false );
    }

    /**
     * for action column
     */
    function column_action($item){
        return '<div class="btn-group"><a href="#" class="btn btn-info" data-answer_id="'.$item['id'].'" id="view-answer">View</a> <a href="#" class="btn btn-danger" data-answer_id="'.$item['id'].'" id="delete-answer">Delete</a></div>';
    }

    /**
     * @param $item
     * column status
     */
    function column_status($item){
        if($item['status'] == '1'){
            echo '<strong class="badge bg-success" id="status'.$item['id'].'">Published</strong>';
        }else{
            echo '<strong class="badge bg-danger" id="status'.$item['id'].'">Denied</strong>';
        }
    }

    function prepare_items()
    {
        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = count($this->_items);
        $this -> set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page
        ]);
        $data = array_slice($this->_items, ($current_page-1)*$per_page, $per_page);

        $this->items = $data;
        $this->_column_headers = array($this->get_columns(), array(), array());
    }
}