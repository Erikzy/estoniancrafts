<p>
    <a class="btn btn-color-primary button medium-orange-button" href="<?= home_url('my-account/portfolio/edit')?>">Add new portfolio</a>
</p>

<?php if (count($portfolios)) : ?>
<p>
    <table class="product-listing-table">
        <thead>
        <tr>
            <!-- <th>#</th> -->
            <th><?= __('Title', 'ktt') ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        	<?php $i = 0; foreach ($portfolios as $portfolio) : ?>
	            <tr>
	                <!-- <th><?= ++$i ?></th> -->
	   				<td><?= $portfolio->post_title ?></td>
	                <td>
	                    <a class="btn button smaller-gray-button" href="<?= home_url('my-account/portfolio/edit?id='.$portfolio->ID) ?>"><?= __('Edit', 'ktt') ?></a>
	                </td>
	            </tr>
	        <?php endforeach; ?>
        </tbody>
    </table>
</p>
<?php endif; ?>