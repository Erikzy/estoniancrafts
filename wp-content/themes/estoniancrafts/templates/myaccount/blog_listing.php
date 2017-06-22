<?php  $blog_post ='<br>
        <a class="btn" href="'.home_url('my-account/blog/edit').'">Add new post</a>
        <br>
        <table class="table table-hover">
        <thead>
        <tr>
        <th>#</th>
        <th>Title</th>
        <th>Date</th>
        <th>Status</th>
        <th>Action</th>
        </tr>
        </thead>
        <tbody>';
        
        query_posts(array(
            'author'        =>  $current_user->ID,
            'orderby'       =>  'post_date',
            'order'         =>  'DESC',
            'post_status' => 'any',
            'posts_per_page' => -1
        ));
        $i=1;
        while ( have_posts() ) : the_post(); 
            $blog_post .='
            <tr>
            <th scope="row">'.$i.'</th>
            <td>'.get_the_title().'</td>
            <td>'.get_the_date().'</td>
            <td>'.get_post_status().'</td>
            <td>';
        
            $blog_post .= (get_post_status() != 'publish' && get_post_status() != 'pending') ? '<a class="btn btn-primary btn-sm" href="'.home_url('my-account/blog/edit?id='.get_the_ID()).'">Edit</a>' : '' ;
            $blog_post .='</td>
            </tr>';
        
            $i++;
        endwhile;
        $blog_post .= '</tbody>
        </table>';

        wp_reset_query();
?>