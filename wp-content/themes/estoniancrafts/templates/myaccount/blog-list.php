<p>
    <a class="btn btn-color-primary" href="<?= home_url('my-account/blog/edit')?>">Add new post</a>
</p>

<p>
    <table class="product-listing-table ">
        <thead>
        <tr>
           <!--  <th>#</th> -->
            <th><?= __('Title', 'ktt') ?></th>
            <th><?= __('Date', 'ktt') ?></th>
            <th><?= __('Status', 'ktt') ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
            <?php
                $current_user = wp_get_current_user();
                query_posts([
                    'author'        =>  $current_user->ID,
                    'orderby'       =>  'post_date',
                    'order'         =>  'DESC',
                    'post_status' => 'any',
                    'posts_per_page' => -1
                ]);
                $i=1;
                while ( have_posts() ) : the_post(); ?>
                    <tr>
                        <!-- <th><?= $i ?></th> -->
                        <td><?= get_the_title() ?></td>
                        <td><?= get_the_date() ?></td>
                        <td><?= get_post_status() ?></td>
                        <td>
                            <?php if ( !in_array(get_post_status(), ['pending', 'publish']) ): ?>
                                <a class="btn" href="<?= home_url('my-account/blog/edit?id='.get_the_ID()) ?>"><?= __('Edit', 'ktt') ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php
                    ++$i;
                endwhile;

                wp_reset_query();
            ?>
        </tbody>
    </table>
</p>
