<?php include 'header.php';?>
<link rel="stylesheet" type="text/css" href="<?=config_item('shop_url')?>static/css/account.css?t=<?=time()?>">
<div class="hls-page">
	<section class="up">
		<header class="header">
			<img class="hls-photo" src="<?=isset($userinfo->headimgurl) ? $userinfo->headimgurl : '#'?>" />
			<p class="nickname">
				<span class="name-str"><?=$userinfo->nickName?></span>
			</p>
			<div class="btn-container dis-flex">
				<button id="btnCards" class="flex-item active">我的乐券</button>
				<button id="btnTranslogs" class="flex-item">交易明细</button>
			</div>
			<div class="card-num-info dis-flex">
				<div class="flex-item single_cards">
					<p><span id="singleNum">
						<?php if ($cards[0]->cardId === 0):?>
							0
						<?php else:?>
							<?=$singleCardsNum?>
						<?php endif;?>
					</span> 种</p>
					<p>单张乐券</p>
				</div>
				<div class="flex-item group_cards">
					<p><span id="groupNum">
						<?php if ($groupBonusCards[0]->cardId === 0):?>
							0
						<?php else:?>
							<?=count($groupBonusCards)?>
						<?php endif;?>
					</span> 种</p>
					<p>组合乐券</p>
				</div>
				<div class="flex-item transfer_cards">
					<p><span id="transferNum">
						<?php if ($cards[0]->cardId === 0):?>
							0
						<?php else:?>
							<?=$allowTransferNum?>
						<?php endif;?>
					</span> 种</p>
					<p>可转移乐券</p>
				</div>
			</div>
			<script type="text/javascript">
				$('#btnCards').on('touchend', function() {
					$('.btn-container button').removeClass('active');
					$(this).addClass('active');
					$('article').addClass('hls-hidden');
					$('#articleCards').removeClass('hls-hidden');
				});
				$('#btnTranslogs').on('touchend', function() {
					$('.btn-container button').removeClass('active');
					$(this).addClass('active');
					$('article').addClass('hls-hidden');
					$('#articleLogs').removeClass('hls-hidden');
				});
			</script>
		</header>
		<article id="articleCards">
			<div class="bundary"></div>
			<div id="single_cards" class="card-category-container">
				<p class="card-type text-left">单张乐券，快捷兑换</p>
				<p class="card-extra text-left">即兑即得，无需集齐</p>
				<ul class="card-list single-cards">
					<?php
						foreach ($singleCards as $card) {
							if ($card->cardId === 0) {
								break;
							}
							print 
							'<li>
								<div class="dis-flex">
									<div class="flex-item">
										<div class="card-title">'. $card->title .'</div>
										<div class="card-num"><span>'. $card->num .'</span> 张</div>
									</div>
									<div class="flex-item btn-transfer">
										<span></span>
										<button class="card-single empty-'.$card->num.'" data-id="'. $card->cardId .'" 
											data-num="'. $card->num .'" data-type="'. $card->cardType .'" 
											data-quantity="'. $card->pointQuantity .'">兑 换</button>
									</div>
								</div>
							</li>';
						}

					?>
				</ul>
			</div>
			<div class="bundary"></div>
			<div id="group_cards" class="card-category-container">
				<p class="card-type text-left">组合乐券</p>
				<p class="card-extra text-left">集齐乐券获利更多</p>
				<ul class="card-list group-cards">
					<?php
						foreach ($groupBonusCards as $card) {
							if ($card->cardId === 0) {
								break;
							}
							print 
							'<li>
								<div class="dis-flex">
									<div class="flex-item">
										<div class="card-title">'. $card->groupName .'</div>
										<div class="card-num"><span>'. $card->num .'</span> 张</div>
									</div>
									<div class="flex-item btn-transfer">
										<span></span>
										<button class="card-group empty-'.$card->num.'" data-id="'. $card->parentId .'" 
											data-type="2" data-num="'. $card->num .'" 
											data-quantity="'. $card->bonusQuantity .'">兑 换</button>
									</div>
								</div>
							</li>';
						}

					?>
				</ul>
			</div>
			<div class="bundary"></div>
			<div id="transfer_cards" class="card-category-container">
				<p class="card-type text-left">可转移乐券，快捷兑换</p>
				<p class="card-extra text-left">转移他人，快捷方便</p>
				<ul class="card-list transfer-cards">
					<?php
						foreach ($allowTransferCards as $card) {
							if ($card->cardId === 0) {
								break;
							}
							print 
							'<li>
								<div class="dis-flex">
									<div class="flex-item">
										<div class="card-title">'. $card->title .'</div>
										<div class="card-num"><span>'. $card->num .'</span> 张</div>
									</div>
									<div class="flex-item btn-transfer">
										<span></span>
										<button class="card-single empty-'.$card->num.'" data-id="'. $card->cardId .'" 
											data-num="'. $card->num .'" data-type="'. $card->cardType .'" 
											data-quantity="'. $card->pointQuantity .'">转 移</button>
									</div>
								</div>
							</li>';
						}

					?>
				</ul>
			</div>
		</article>
		<article id="articleLogs" class="hls-hidden">
			<div class="bundary"></div>
			<ul id="trans_list" class="trans-list">
				<!-- List Container-->
			</ul>
		</article>
	</section>
	<div class="copyright"><a href="/about.html" target="_blank">爱创科技</a> · 提供技术支持</div>
</div>
<input id="hidden_input_role" type="hidden" value="<?=$role?>">
<input type="hidden" name="role" id="role_str" value="<?=$role_str?>">
<script type="text/javascript" src="<?=config_item('shop_url')?>static/js/account.js?t=<?=time()?>"></script>
<?php include 'footer.php';?>