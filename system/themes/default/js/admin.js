// Admin functions
jQuery.fn.extend({
	insertAtCaret: function(myValue){
	  return this.each(function(i) {
	    if (document.selection) {
	      //For browsers like Internet Explorer
	      this.focus();
	      sel = document.selection.createRange();
	      sel.text = myValue;
	      this.focus();
	    }
	    else if (this.selectionStart || this.selectionStart == '0') {
	      //For browsers like Firefox and Webkit based
	      var startPos = this.selectionStart;
	      var endPos = this.selectionEnd;
	      var scrollTop = this.scrollTop;
	      this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
	      this.focus();
	      this.selectionStart = startPos + myValue.length;
	      this.selectionEnd = startPos + myValue.length;
	      this.scrollTop = scrollTop;
	    } else {
	      this.value += myValue;
	      this.focus();
	    }
	  })
	},
	
	selectImageDefinition: function(filename, title) {
		return this.each(function(i) {
			var t = this.value;
			var st = filenameToWikiRef(filename);
			var start = t.indexOf(st);
			if (start > -1) { 
				if (document.selection) {
					var inputRange = this.createTextRange ();
	                inputRange.moveStart("character", start);
	                inputRange.collapse();
	                inputRange.moveEnd("character", st.length);
	                inputRange.select();
				}
				else {
					this.selectionStart = start;
					this.selectionEnd = start + st.length;
					this.focus();
				}
			}
		})
	}
});

$(document).ready(function(){
	$("#uploadTarget").load(function() {
		var id = $("#id").val();
		var fname, fnamewoext;
		fname = $("#btnOpenFile").val().replace(/^.*[\\\/]/, '');
		//fnamewoext = filenameToTitle(fname);
		getContentImages(id);
		$("#text").insertAtCaret(' '+filenameToWikiRef(fname)+' ');
	});
	
	$("#btnOpenFile").change(function (){
		$("#snake").show();
		$("#btnSendFile").click();
	});
	
	getContentImages($("#id").val());
	$("#snake").hide();
});


function addFile() {
	$("#btnOpenFile").click();
	return false;
}

function getContentImages(contentId) {
	if (contentId == null)
		contentId = $("#id").val();
	$.getJSON('/api?getImagesFor='+contentId, function(data) {
		var s = "";
		$.each(data, function(key, val){
			s += "<img src='/images/" + contentId + "/" + val + "' title='" + filenameToTitle(val) + "' height='60' onclick='selectImage(this);' />&nbsp;"
		})
		$("#imgList").html(s);
	});
	$("#snake").hide();
}

function selectImage(obj) {
	$(".imagelist img.active").removeClass("active");
	$(obj).addClass("active")
	$("#text").selectImageDefinition($(obj).attr('src'), $(obj).attr('title'));
	$("#pnlImageOptions").show();
}

function deleteFile() {
	var obj = $(".imagelist img.active");
	if (obj) {
		$("#btnDeleteFile").hide();
		$("#snake").show();
		$(obj).removeClass("active");
		
		$.ajax({
			url: "/api?deleteImage=" + $(obj).attr("src"),
			complete: function() {
				getContentImages();
			}
		});
	}
	return false;
}

function insertFileRef() {
	var obj = $(".imagelist img.active");
	if (obj) {
		$("#text").insertAtCaret(' '+filenameToWikiRef($(obj).attr("src"))+' ');
	}
	return false;
}

function filenameToTitle(s) {
	var f = s;
	if (f.indexOf('/') != -1)
		f = f.substr(f.lastIndexOf('/')+1);
	return ( f.lastIndexOf('.') > -1 ? f.substr(0, f.lastIndexOf('.')) : s ).replace(/\_/g, ' ');
}

function filenameToWikiRef(filename) {
	if (filename == "")
		return "";
	var id = $("#id").val();
	var path = filename;
	if (path.indexOf('/images/') == -1)
		path = '/images/' + id + '/' + path;
	return '![' + filenameToTitle(filename) + '](' + path + ' "' + filenameToTitle(filename) + '")';
}
