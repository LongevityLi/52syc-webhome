<?php
$title = '收录管理';
include './head.php';
$applyrows=$DB->num_rows($DB->query("SELECT * FROM `lylme_apply`"));
$grouplists = $DB->query("SELECT * FROM `lylme_groups`");
?>
<script src="../assets/js/svg.js"></script>
<style>td img,td svg.icon {
	width: 35px;
	height: 35px;
}
</style>
 <main class="lyear-layout-content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
<?php
$set = isset($_GET['set']) ? $_GET['set'] : null;
if ($set == 'edit') {
	$id = $_GET['id'];
	$row2 = $DB->query("select * from lylme_apply where apply_id='$id' limit 1");
	$row = $DB->fetch($row2);
	echo '<h4>修改链接信息</h4>
<div class="panel-body">
<form action="./apply.php?set=edit_submit&id=' . $id . '" method="POST">
<div class="form-apply">
<label>*名称:</label><br>
<input type="text" class="form-control" name="apply_name" value="' . $row['apply_name'] . '" required>
</div>
<div class="form-apply">
<label>*链接:</label><br>
<input type="text" class="form-control" name="apply_url" value="' . $row['apply_url'] . '" required>
</div>
<div class="form-apply">
<label>图标:</label><br>
<textarea type="text" class="form-control" name="apply_icon">' . $row['apply_icon'] . '</textarea>
<small class="help-block">方式1：填写图标的<code>URL</code>地址，如<code>/img/logo.png</code>或<code>http://www.xxx.com/img/logo.png</code><br>
方式2：粘贴图标的<code>SVG</code>代码，<a href="./help.php?doc=icon" target="_blank">查看教程</a><br>方式3：留空使用默认图标<br><b>注：修改为svg代码后审核列表可能存在显示异常，不会影响首页效果，忽略即可！</b></small>

</div>
<div class="form-group">
<label>*分组:</label><br>
<select class="form-control" name="apply_group">';
	while ($grouplist = $DB->fetch($grouplists)) {
		if ($grouplist["group_id"] == $row['apply_group']) {
			$select = 'selected="selected"';
		} else {
			$select = '';
		}
		echo '<option  value="' . $grouplist["group_id"] . '" ' . $select . '>' . $grouplist["group_id"] . ' - ' . $grouplist["group_name"] . '</option>';
	}
	echo '</select>
</div>
<div class="form-apply">
<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form>
</div>
<br/><a href="./apply.php"><<返回收录管理列表</a></div></div>';
} elseif ($set == 'conf') {
	echo '<h4>修改收录设置</h4>
<div class="panel-body">
<form action="./apply.php?set=conf_submit" method="POST">
<div class="form-group" id="apply">
<label class="btn-block" for="web_yan_status">申请收录</label>
<label class="lyear-switch switch-solid switch-cyan">
<select class="form-control" name="apply">
<option ';
if($conf['apply'] == 0) echo 'selected="selected"';
echo 'value="0">开启-需要审核</option><option ';
if($conf['apply'] == 1) echo 'selected="selected"';
echo 'value="1">开启-无需审核</option><option ';
if($conf['apply'] == 2) echo 'selected="selected"'; 
echo 'value="2">关闭-关闭申请</option>
</select>  
                      </label>
                      <small class="help-block">申请收录开关，地址：<code>'. siteurl().'/apply</code><br>前往<a href="/apply" target="_blank">申请收录</a>提交页</small>
                    </div>     
<div class="form-group">
     <label for="apply_gg">收录页公告</label>
                      <textarea width="200px" type="text" rows="5" class="form-control" name="apply_gg" placeholder="显示在收录页的公告">'.$conf['apply_gg'].'</textarea>
               <small class="help-block">显示在收录页的公告<code>使用HTML代码编写</code></small>
                    </div>
<div class="form-apply">
<input type="submit" class="btn btn-primary btn-block" value="保存"></form>
</div>
<br/><a href="./apply.php"><<返回收录管理列表</a></div></div>';    
    
}elseif ($set == 'conf_submit') {
    $apply= $_POST['apply'];
    $apply_gg= $_POST['apply_gg'];
    saveSetting('apply',$apply); 
    saveSetting('apply_gg',$apply_gg); 
    echo '<script>alert("修改成功！");window.location.href="./apply.php";</script>';
}
elseif ($set == 'edit_submit') {
	$id = $_GET['id'];
	$rows2 = $DB->query("select * from lylme_apply where apply_id='$id' limit 1");
	$rows = $DB->fetch($rows2);
	if (!$rows) echo '<script>alert("当前记录不存在！");history.go(-1);</script>';
	$name = $_POST['apply_name'];
	$icon = $_POST['apply_icon'];
	$url = $_POST['apply_url'];
	$group =  $_POST['apply_group'];
	if ($name == NULL) {
		echo '<script>alert("保存错误,请确保带星号的都不为空！");history.go(-1);</script>';
	} else {
		$sql = "UPDATE `lylme_apply` SET `apply_name` = '" . $name . "', `apply_group` = '" . $group . "',`apply_icon` = '" . $icon . "',`apply_url` = '" . $url . "' WHERE `lylme_apply`.`apply_id` = '" . $id . "';";
		if ($DB->query($sql)) echo '<script>alert("修改 ' . $name . ' 成功！");window.location.href="./apply.php";</script>'; else echo '<script>alert("' . $sql . '修改失败！原因：\n'.$DB->error().'");history.go(-1);</script>';
	}
} elseif ($set == 'delete') {
	$id = $_GET['id'];
	$delsql = 'DELETE FROM `lylme_apply` WHERE apply_id =' . $id;
	if ($DB->query($delsql)) echo '<script>alert("删除成功！");window.location.href="./apply.php";</script>'; else echo '<script>alert("删除失败！");history.go(-1);</script>';
} elseif ($set == 'status') {
	$id = $_GET['id'];
	$sw = $_GET['sw'];
	$sql = "UPDATE `lylme_apply` SET `apply_status` = '".$sw."' WHERE `lylme_apply`.`apply_id` = ".$id.";";
	if($sw==1) {
		if ($DB->query($sql)) {
			$applyres =  $DB->get_row("SELECT * FROM `lylme_apply` WHERE `apply_id` = ".$id);
			$name=strip_tags(daddslashes($applyres['apply_name']));
			$url=strip_tags(daddslashes($applyres['apply_url']));
			$icon=daddslashes($applyres['apply_icon']);
			$group_id=strip_tags(daddslashes($applyres['apply_group']));
			$mail=strip_tags(daddslashes($applyres['apply_mail']));
			$link_order = $DB->count('select MAX(id) from `lylme_links`')+1;
			$sql1 = "INSERT INTO `lylme_links` (`id`, `name`, `group_id`, `url`, `icon`, `PS`,`link_order`) VALUES (NULL, '" . $name . "', '" . $group_id . "', '" . $url . "', '" . $icon . "', '" . $mail . "的提交 ', '" . $link_order . "');";
			if($DB->query($sql1)) {
				echo '<script>alert("成功！网站已成功收录！");window.location.href="./apply.php";</script>';
			} else {
				echo '<script>alert("收录失败！错误原因：\n'.$DB->error().'");history.go(-1);</script>';
			}
		}
	} else if($sw==2){
		if ($DB->query($sql)) {
			echo '<script>window.location.href="./apply.php";</script>';
		}
		else{
		    echo '<script>alert("审核失败！原因：\n'.$DB->error().'");history.go(-1);</script>';
		}
	}
	else{
	     echo '<script>alert("审核失败！未知参数");history.go(-1);</script>';
	}
} else {
     echo '<pre>'.$conf['apply_gg'].'<br><a href="./apply.php?set=conf">修改</a></pre>';
	echo '<div class="alert alert-info">
    收录申请统计： <b>' . $applyrows . '</b> 次<br/>
    收录申请开关： <b>';

    switch ($conf['apply']) {
        case 0:
            echo '开启-需要审核';
        break;
        case 1:
            echo '开启-无需审核';
        break;
        case 2:
            echo '关闭-关闭申请';
        break;
    }
    echo '</b> &nbsp;<a href="./apply.php?set=conf">修改设置</a></div>';
   
	?>
		      <div class="table-responsive">
		        <table class="table table-striped">
		          <thead><tr><th>序号</th><th>图标</th><th>名称</th><th>链接</th><th>分组</th><th>审核</th><th>操作</th><th>申请时间</th></tr></thead>
		          <tbody>
		<?php
		    $rs = $DB->query("SELECT * FROM `lylme_apply` ORDER BY `lylme_apply`.`apply_time` DESC");
	$i=0;
	while ($res = $DB->fetch($rs)) {
		$i++;
		echo '<tr><td>';
		if($res["apply_status"]==0) {echo '<font color="#48b0f7"><b>'.$i.'</b></font>';}
		else{echo '<b>'.$i.'</b>';}
		echo '</td><td>';
		if(empty($res["apply_icon"])){
		    echo '未提交图标';
		}else{ echo '<img src="' . $res["apply_icon"] . '" alt="' . $res["apply_name"] . '" />';}
		echo '</td><td>' . $res['apply_name'] . '</td><td>' . $res['apply_url'] .'</td><td>'.$DB->fetch($DB->query("SELECT * FROM `lylme_groups` WHERE `group_id` = " . $res['apply_group'])) ["group_name"].'
		</td><td>';
		if($res["apply_status"]==2) {
			echo '<font color="#f96868">已拒绝</font>';
		} else if($res["apply_status"]==1) {
			echo '<font color="#3c763d">已通过</font>';
		} else {
			echo '
    <a href="./apply.php?set=status&id=' . $res['apply_id'] . '&sw=1" class="btn btn-primary btn-xs" onclick="return confirm(\'是否通过该条申请？\');">通过</a>&nbsp; 
    <a href="./apply.php?set=status&id=' . $res['apply_id'] . '&sw=2" class="btn btn-cyan btn-xs" onclick="return confirm(\'是否拒绝该条申请 ？\');">拒绝</a>';
		}
		echo '</td><td>';
		if($res["apply_status"]==0) {echo '<a href="./apply.php?set=edit&id=' . $res['apply_id'] . '" class="btn btn-info btn-xs">编辑</a>&nbsp;';}
		echo '<a href="./apply.php?set=delete&id=' . $res['apply_id'] . '" class="btn btn-xs btn-danger" onclick="return confirm(\'确定删除 ' . $res['apply_name'] . ' 的记录吗？\');">删除</a> </td>
        <td>'.$res['apply_time'].'</td>
        </tr>';
	}
	?>
		          </tbody>
		        </table>
		      </div>
		                    </div>
		            </div>
		          </div>
		        </div>
		      </div>
		    </main>
		<?php
}
include './footer.php';
?>