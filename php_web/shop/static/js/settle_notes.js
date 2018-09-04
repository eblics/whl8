/**
 * 核销记录界面
 * 
 * @author shizq
 * 
 */
$(function() {
	
	var currentPage = 1;
	var more = false;
	
	function init() {
		getNotes();
		bindEvent();
	}

	function bindEvent() {
		$(document).scroll(function() {
			var h = $(this).height(); // 内容的高度
			if (h - $(this).scrollTop() <= document.body.clientHeight) {
				if (more) {
					currentPage++;
					getNotes();
				}
			}
		});
	}
	
	function getNotes() {
		var params = {
			page: currentPage
		};
		hls.api.Settle.settle_notes(params, function(resp) {
			if (resp.length == 20) more = true;
			var transList = [];
			resp.forEach(function(note) {
				var obj = {title: note['title'], num: note['num']};
				if (transList[note['id']]) {
					transList[note['id']]['data'].push(obj);
				} else {
					transList[note['id']] = {};
					transList[note['id']]['date'] = dateInfo(note.submitTime);
					transList[note['id']]['state'] = note.state;
					transList[note['id']]['settleCode'] = note.settleCode;
					transList[note['id']]['data'] = [];
					transList[note['id']]['data'].push(obj);
				}
				
			});
			viewAdapter(transList);
		}, function(errmsg) {
			hls.util.Dialog.showErrorMessage(errmsg, function() {
				history.back();
			});
		});
	}
	
	function viewAdapter(notes) {
		notes = notes.reverse();
		if (notes.length <= 0) {
			var p_title = $('<p></p>').addClass('list-title').text('你没有任何核销记录！');
			$('#list_container').append(p_title)
			return;
		}
		var currentMonth;
		for (var index in notes) {
			var note = notes[index];
			if (currentMonth != note.date[2]) {
				currentMonth = note.date[2];
				var p_title = $('<p></p>').addClass('list-title').text(note.date[2]);
				var ul = $('<ul></ul>').addClass('list');
			}

			var li = $('<li></li>').addClass('list-item');
			var div = $('<div></div>').text(note.date[0]);
			var span = $('<span></span>').text(note.date[1]);
			var p = $('<p></p>').text('核销 ');
			div.append(span);
			note.data.forEach(function(item) {
				p.append(item.num + '张 ');
				p.append(item.title + ' ');
			});
			var span_state = $('<span></span>');
			if (note.state == 0) {
				span_state.text('审核中');
			} else {
				if (note.settleCode != 1) {
					span_state.text('　失败').addClass('faild');
				} else {
					span_state.text('　成功').addClass('success');
				}
			}
			li.append(div).append(p).append(span_state);
			ul.append(li);
			$('#list_container').append(p_title).append(ul);
		}
	}

	function numToDay(num) {
		switch (num) {
			case 0:
				return '周日';
			case 1:
				return '周一';
			case 2:
				return '周二';
			case 3:
				return '周三';
			case 4:
				return '周四';
			case 5:
				return '周五';
			case 6:
				return '周六';
		}
	}

	function dateInfo(theTime) {
		var year = theTime.substr(0, 4);
		var month = theTime.substr(5, 2);
		var day = theTime.substr(8, 2);
		var time = theTime.substr(11, 5);
		var date = new Date(year, month - 1, day);
		var xingqi = numToDay(date.getDay());
		var riqi = month + '-' + day;
		var today = new Date();
		var yuefen = date.getMonth() + 1 + '月';

		if (today.getFullYear() === date.getFullYear() 
			&& today.getMonth() === date.getMonth()) {
			yuefen = '本月';
		}
		if (today.getFullYear() === date.getFullYear() 
			&& today.getDate() === date.getDate()) {
			xingqi = '今天';
			riqi = time;
		}
		if (today.getFullYear() === date.getFullYear() 
			&& today.getDate() - 1 === date.getDate()) {
			xingqi = '昨天';
			riqi = time;
		}
		return [xingqi, riqi, yuefen, year, month];
	}
	
	init();
});