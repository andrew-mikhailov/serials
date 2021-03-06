<?php $__env->startSection('title', 'Create asset'); ?>

<?php $__env->startSection('dashboard_content'); ?>
<div class="row">
    <div class="col-xs-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="title">Create asset</div>
                </div>
            </div>
            <div class="card-body">

                <?php if(count($errors) > 0): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach($errors->all() as $error): ?>
                        <li><?php echo e($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                <?php echo e(Form::open(array('url' => '/admin/dashboard/assets/' . $asset->id . '/edit'))); ?>

                    <?php echo csrf_field(); ?>

                    <div class="control">
                        <div class="sub-title">Title</div>
                        <?php echo e(Form::text('title', $asset->title, array('class' => 'form-control'))); ?>

                    </div>
                    <div class="control">
                        <div class="sub-title">Original title</div>
                        <?php echo e(Form::text('original_title', $asset->original_title, array('class' => 'form-control'))); ?>

                    </div>
                    <div class="control">
                        <div class="sub-title">Description</div>
                        <?php echo e(Form::textarea('plot', $asset->plot, ['class' => 'form-control'])); ?>

                    </div>
                    <div class="control">
                        <div class="sub-title">Plot</div>
                        <?php echo e(Form::textarea('description', $asset->description, ['class' => 'form-control'])); ?>

                    </div>
                    <div class="control">
                        <div class="sub-title">Body</div>
                        <?php echo e(Form::textarea('body', $asset->body, ['class' => 'form-control'])); ?>

                    </div>
                    <div class="control">
                        <div class="sub-title">Start date</div>
                        <?php echo e(Form::text('start_date', $asset->start_date, array('class' => 'form-control'))); ?>

                    </div>
                    <div class="control">
                        <div class="sub-title">End date</div>
                        <?php echo e(Form::text('end_date', $asset->end_date, array('class' => 'form-control'))); ?>

                    </div>
                    <div class="control">
                        <div class="sub-title">Tags</div>
                        <?php echo e(Form::text('end_date', $asset->end_date, array('class' => 'form-control'))); ?>

                    </div>
                    <div class="">
                        <div class="sub-title">Tags</div>
                        <?php echo e(Form::select('tags', $tags_list, $selected_tags, array('multiple'=>'multiple', 'name'=>'tags[]'))); ?>

                    </div>
                    <div class="">
                        <div class="sub-title">Genres</div>
                        <?php echo e(Form::select('genres', $genres_list, $selected_genres, array('multiple'=>'multiple', 'name'=>'genres[]'))); ?>

                    </div>
                    <div class="login-button text-center">
                        <?php echo e(Form::submit('Create', array('class' => 'btn btn-primary'))); ?>

                    </div>
                <?php echo e(Form::close()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>