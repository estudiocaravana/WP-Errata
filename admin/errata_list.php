<?php 

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class ErrataList extends WP_List_Table {

	public function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( 
	        array(
	            'singular'  => __('errata','ecerpl'),     
	            'plural'    => __('erratas','ecerpl'),    
	            'ajax'      => true
				) 
		);
    }

    public function column_default($item, $column_name){
    	return stripslashes($item->__get($column_name));
	}
	
	public function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  
            /*$2%s*/ $item->__get('id')
        );
    }

    public function column_id($item){
    	$html = $item->__get('id') . '

    	<div class="row-actions">';
    	if ($item->__get('postID')){
    		$html.='<span class="edit"><a href="post.php?post='.$item->__get('postID').'&action=edit" title="Edit this item">Edit</a> | ';
    	}
    	$html.='</span><span><a href="tools.php?page=errata_list&amp;errata_action=fix&amp;errata_id='.$item->__get('id').'">Mark as fixed</a>
    		 | </span><span class="trash"><a href="tools.php?page=errata_list&amp;errata_action=delete&amp;errata_id='.$item->__get('id').'">Trash</a>     		 
    		 | </span><span class="view"><a href="'.add_query_arg('errata_path',$item->__get('path'),$item->__get('url')).'" title="View" rel="permalink">View</a></span>
    	</div>';

    	return $html;
    }

    public function get_columns(){
        $columns = array(
            'cb'     		=> '<input type="checkbox" />',
            'id'    		=> __('ID','ecerpl'),
            'date'			=> __('Date','ecerpl'),
            'errata'		=> __('Errata','ecerpl'),
            'correction'	=> __('Correction','ecerpl'),
            'ip'			=> __('IP','ecerpl')            
        );
        return $columns;
    }

    public function get_sortable_columns() {
        $sortable_columns = array(
            'date'     => array('date',true)
        );
        return $sortable_columns;
    }

    public function process_errata_list_action(){
		if (isset($_REQUEST['page']) &&
			isset($_REQUEST['errata_id']) &&
			isset($_REQUEST['errata_action']) && 
			$_REQUEST['page'] == 'errata_list'){

				require_once (WP_ERRATA_PATH . 'model/wp_model.php');
				$model = new ErrataModel();

				//TODO Add bulk action

				if (is_array($_REQUEST['errata_id'])){
					foreach ($_REQUEST['errata_id'] as $errata){
						$this->process_errata_action($errata,$_REQUEST['errata_action'],$model);
					}
				}
				else{
					$this->process_errata_action($_REQUEST['errata_id'],$_REQUEST['errata_action'],$model);
				}
				wp_redirect( remove_query_arg(array("errata_action","errata_id"), wp_get_referer()) );
				exit();			
		}
	}

	private function process_errata_action($errata,$action,$model){
		switch ($action) {
			case 'fix':
				$model->fixErrata($errata);
			break;

			case 'delete':
				$model->deleteErrata($errata);
			break;
		}
	}

    public function process_bulk_action() {
        
    }

    public function prepare_items($data) {
        
        $per_page = 5;
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bulk_action();
        
        if ($data){
	        function usort_reorder($a,$b){
	            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to title
	            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
	            $result = strcmp($a->__get($orderby), $b->__get($orderby)); //Determine sort order
	            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
	        }
        	usort($data, 'usort_reorder');
		}
        
        $current_page = $this->get_pagenum();
        
        $total_items = $data ? count($data) : 0;
        
        if ($data)
			$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        
        $this->items = $data;
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)   
        ) );
    }

	public function showErrataList(){

		require_once (WP_ERRATA_PATH . 'model/wp_model.php');

		$view = "";

		if (isset($_GET["errata_view"])){
			$view = $_GET["errata_view"];

			if ($view!="fixed" && $view!="deleted"){
				$view = "";
			}
		}

		$model = new ErrataModel();
		$this->prepare_items($model->getErratas($view));
	?>

		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php _e('All Errata','ecerpl'); ?></h2>

			<ul class="subsubsub">
				<li><a href="tools.php?page=errata_list" <?php if (!$view){ ?>class="current"<?php } ?> ><?php _e('Active','ecerpl') ?></a>|</li>
				<li><a href="tools.php?page=errata_list&amp;errata_view=fixed" <?php if ($view == 'fixed'){ ?>class="current"<?php } ?> ><?php _e('Fixed','ecerpl') ?></a>|</li>
				<li><a href="tools.php?page=errata_list&amp;errata_view=deleted" <?php if ($view == 'deleted'){ ?>class="current"<?php } ?> ><?php _e('Deleted','ecerpl') ?></a>|</li>
			</ul>
			
			<form id="erratas-form" method="get">
				
				<?php if (isset($_REQUEST['page'])) {?>        
	            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	            <?php } ?>
	                    
	            <?php $this->display(); ?>
	        </form>		
		</div>

	<?php

	}

}

?>