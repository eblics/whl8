<!-- 报表公用筛选菜单 -->
<div class="rptmain" style="border:none">
<div class="rptcontent" <?=get_current_router(1)=='charts' && in_array(get_current_router(2),['trend','scan']) ? 'id="trend_condition"' : ''?>>
<div class="tool">
  <p class="ltitle">年</p>
  <div class="rtitle">
      <ul class="year">
          <?php for($i=2015;$i<=date('Y');$i++){?>
                <li><a href="javascript:;" data-value="<?php echo $i;?>" <?php if(date('Y')==$i) echo 'class="active"'?>><?php echo $i; ?>年</a></li>
          <?php }?>
      </ul>
  </div>
</div>
<div style="clear:both"></div>
<div class="tool">
  <p class="ltitle">月</p>
  <div class="rtitle">
      <ul class="month">
          <?php if(get_current_router(1)=='charts' && in_array(get_current_router(2),['userrank',])){?>
            <li><a href="javascript:;" data-value="0">全部</a></li>
          <?php }?>
          <?php for($i=1;$i<=12;$i++){?>
                <li><a href="javascript:;" data-value="<?php echo sprintf("%02d",$i);?>" <?php if(date('m')==$i) echo 'class="active"'?>><?php echo $i;?>月</a></li>
          <?php }?> 
      </ul>
  </div>
</div>
<div style="clear:both"></div>
<div class="tool" id="weeklist">
  <p class="ltitle"></p>
  <div class="rtitle" style="margin-left:55px;">
      <ul class="week"></ul>
  </div>
</div>
<div style="clear:both"></div>
<?php if(get_current_router(1)=='charts' && in_array(get_current_router(2),['period','region','scan'])):?>
  <div class="tool" id="daylist">
    <p class="ltitle"></p>
    <div class="rtitle" style="margin-left:55px;">
      <ul class="day"></ul>
    </div>
  </div>
  <div style="clear:both"></div>
<?php endif; ?>
<?php 
    if(get_current_router(1)=='charts' && in_array(get_current_router(2),['trend','scan'])){
      ?>
      <hr style="height:1px;border:none;border-top:1px solid #e0e0e0;margin:0px">
  <?php }?>
</div>
<!-- 省市区开始 -->
<div class="rptcontent" <?=get_current_router(1)=='charts' && in_array(get_current_router(2),['trend','scan']) ? 'id="trend_situation"' : ''?>>

  <?php
    if(get_current_router(1)=='charts' && in_array(get_current_router(2),['index','userscan','userrank','trend','scan'])){
  ?>
<div class="tool">
  <p class="ltitle">省份</p>
  <div class="rtitle">
      <ul>
          <select id="proCode" class="select select2" name="proCode">
            <option value='0'>全国</option>
            <?php foreach ($data['data'] as $pro):?>
            <?php if($pro->code!=='710000'&&$pro->code!=='810000'&&$pro->code!=='820000'){?>
              <option value="<?php echo $pro->code;?>" class="txtiundefined"><?php echo $pro->name;?></option>
            <?php }?>
              <?php endforeach;?>
            </select>
      </ul>
  </div>
  <p class="ltitle">城&nbsp;&nbsp;&nbsp;市</p>
  <div class="rtitle">
      <ul>
          <select style="margin-left: 20px;" id="cityCode" class="select select2" name="cityCode">
              <option value='0'>全部</option>
            </select>
      </ul>
  </div>
  <?php
    if(get_current_router(1)=='charts' && in_array(get_current_router(2),['index','userscan','trend'])){
  ?>
  <p class="ltitle">区&nbsp;&nbsp;&nbsp;县</p>
  <div class="rtitle">
      <ul>
          <select style="margin-left: 20px;" id="areaCode" class="select select2" name="areaCode">
              <option value='0'>全部</option>
            </select>
      </ul>
  </div>
  <?php }?>
  </div>
  <?php }?> 
  <!-- 活动列表-产品分类 -->
  <?php
    if(get_current_router(1)=='charts' && in_array(get_current_router(2),['policy'])){
  ?>
  <div class="tool">
  <p class="ltitle">活动</p>
  <div class="rtitle">
      <ul>
          <select id="activityid" class="select" name="activityid" style="width:200px;">
            <?php foreach ($data['activity'] as $v):?>
            <?php if(isset($v->parentId)){?>
            <option value="<?php echo $v->id;?>" style="color:#000;">　　<?php echo $v->name;?></option>
            <?php }else{?>
              <option value="<?php echo $v->id;?>" disabled="disabled" style="color:#000;font-weight:bold"><?php echo $v->name;?></option>
            <?php }?>
              <?php endforeach;?>
          </select>
      </ul>
  </div>
  <p class="ltitle">产品分类</p>
  <div class="rtitle">
      <ul>
          <select id="categoryid" class="select" name="categoryid" style="width:200px;">
            <option value='0'>全部</option>
            <?php foreach ($data['category'] as $c):?>
            <option value="<?php echo $c['id'];?>" style="color:#000;">
            <?php 
            $string='';
            for($i=0;$i<$c['level']-1;$i++){
              $string=$string."　";
            }
            echo $string;
            ?><?php echo $c['name'];?></option>
              <?php endforeach;?>
          </select>
      </ul>
  </div>
  </div>
  <?php }?>
  <!-- 产品开始 -->
<div class="tool">
  <p class="ltitle">产品</p>
  <div class="rtitle">
      <ul>
          <select class="select select2" id="productid" name="productid">
            <option value='0'>全部</option>
          </select>
      </ul>
  </div>
  <p class="ltitle">乐码批次</p>
  <div class="rtitle">
      <ul>
          <select class="select select2" id="batchid" name="batchid">
            <option value='0'>全部</option>
          </select>
      </ul>
  </div>
  <?php 
    if(get_current_router(1)=='charts' && in_array(get_current_router(2),['userscan','period','region','business','useranalysis','business','userrank','index','policy','scan'])){
      ?>
      <div id="getSearch" class="btn btn-blue">查询</div>
      <?php
        if(!in_array(get_current_router(2),['scan'])):
      ?>
      <div id="getDown" class="btn btn-blue">下载</div>
      <?php endif;?>
  <?php }?>
  <?php 
    if(get_current_router(1)=='charts' && in_array(get_current_router(2),['region'])){?>
      <span id="get_daily_down"><input id="is_daily" name="is_daily" type="checkbox" value="0"> 下载日扫码数据</span>
  <?php }?>
  <?php 
    $mchId=$this->session->userdata('mchId');
    $arr=array('0','112','119','126','167','169','171');//贝奇专用
    if(in_array($mchId, $arr)){
      if(get_current_router(1)=='charts' && in_array(get_current_router(2),['userscan','index'])){
        echo '<span id="userscan_detail_down"><input id="is_detail" name="is_detail" type="checkbox" value="0"> 下载用户详细扫码数据</span>';
      }
    }
    ?>
  <?php
    if(get_current_router(1)=='charts' && in_array(get_current_router(2),['trend'])){
  ?>
  <div id="getSearch" class="btn btn-blue">加入</div>
  <?php }?>
</div>
</div>
<!-- 省市区结束 -->
</div>
<div class="h10" style="clear:both"></div>
<div style="border-top:1px solid #e0e0e0;" class="line"></div>
<div class="h20" style="clear:both"></div>
<!-- 报表公用筛选菜单 -->