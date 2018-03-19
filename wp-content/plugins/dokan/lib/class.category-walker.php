<?php 

/**
 *  Dokan Product category Walker Class
 *  @author weDevs
 */
class DokanCategoryWalker extends Walker_Category{

    public function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0 ) {

        $args = wp_parse_args(array(
            'name'    => 'product_cat',
        ), $args);

        extract($args);
        
        ob_start(); ?>   

        <li>
            <span id="<?php print $category->term_id; ?>-handle" class="subcat-togglehandle"  onclick="toggleEcSubcatsEdit(<?php print $category->term_id; ?>)"></span>       
			<?php
				//var_dump($selected);
			
			?>
            <input type="checkbox" onclick="setCategory(<?php print $category->term_id; ?>)" cat-term-name="<?php print esc_attr( $category->name ); ?>" cat-term-id="<?php print $category->term_id; ?>" class="checkbox-category" <?php echo checked( in_array( $category->term_id, $selected ), true ); ?> id="category-<?php print $category->term_id; ?>" name="<?php print $name; ?>2[]" value="<?php print $category->term_id; ?>" />
            <label for="category-<?php print $category->term_id; ?>"  >
                <?php print esc_attr( $category->name ); ?>
            </label>
           <div class="subcats subcats-<?php print $category->term_id; ?>" parentcat-name="<?php print esc_attr( $category->name ); ?>" subcat-id="<?php print $category->term_id; ?>" style="display:none;margin-left:15px" >
        <?php // closing LI is added inside end_el	
        $output .= ob_get_clean();
    }
    
    
    
    public function end_el( &$output, $page, $depth = 0, $args = array() ) {
        if ( 'list' != $args['style'] )
            return;
 
        $output .= "</div></li>\n";
    }

}