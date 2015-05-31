<?php if (!defined('THINK_PATH')) exit(); if(is_array($tree)): $i = 0; $__LIST__ = $tree;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><dl class="cate-item">
		<dt class="cf">
			<form action="<?php echo U('edit','module='.$_GET['module']);?>" method="post">
				<div class="btn-toolbar opt-btn cf">
					<a title="编辑" href="<?php echo U('edit?id='.$list['id'].'&pid='.$list['pid'].'&module='.$_GET['module']);?>">编辑</a>
					<a title="删除" href="<?php echo U('remove?id='.$list['id'].'&module='.$_GET['module']);?>" class="confirm ajax-get">删除</a>
					<a title="移动" href="<?php echo U('operate?type=move&from='.$list['id'].'&module='.$_GET['module']);?>">移动</a>
				</div>
				<div class="fold"><i></i></div>
				<div class="order"><input type="text" name="sort" class="text input-mini" value="<?php echo ($list["sort"]); ?>"></div>
                <?php if(!empty($group["has_admin"])): ?><div class="has_admin"><?php echo (get_mult_username($list["admin_uids"])); ?></div><?php endif; ?>                
				<div class="name">
					<span class="tab-sign"></span>
					<input type="hidden" name="id" value="<?php echo ($list["id"]); ?>">
					<input type="text" name="title" class="text" value="<?php echo ($list["title"]); ?>">
                    
					<a class="add-sub-cate" title="添加子分类" href="<?php echo U('add?pid='.$list['id'].'&module='.$_GET['module']);?>">
						<i class="icon-add"></i>
					</a>
                    
					<span class="help-inline msg"></span>
				</div>
			</form>
		</dt>
		<?php if(!empty($list['_'])): ?><dd>
				<?php echo R('Category/tree', array($list['_']));?>
			</dd><?php endif; ?>
	</dl><?php endforeach; endif; else: echo "" ;endif; ?>