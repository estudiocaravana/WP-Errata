// TODO Unobstructive JavaScript

var com;

if (!com) com = {};

if (!com.estudiocaravana) com.estudiocaravana = {};

com.estudiocaravana.Errata = {};

(function( ){

	var _rootDirPath;
	var _ns = "com-estudiocaravana-errata-";
	var _nsid = "#"+_ns;
	var _selectedRange;
	var _timeout;
	var _allowMouseEvent=true;
	var _letterToken = "LETTER";
	
	function init(){

		var $_GET = _getQueryParams(document.location.search);
		if ($_GET['errata_path']){
			var path = $_GET['errata_path'].split("->");
			_highlightErrata(document,path[1],_ns+"mark "+_ns+"right");
			_highlightErrata(document,path[0],_ns+"mark "+_ns+"left");			
		}

		//We get the plugin root directory path from the <script> tag included in the html document
		_rootDirPath = $(_nsid+"script").attr("src");
		_rootDirPath = _rootDirPath.split("/");
		_rootDirPath = _rootDirPath.slice(0,_rootDirPath.length - 3);
		_rootDirPath = _rootDirPath.join("/");
		//_rootDirPath = "/" + _rootDirPath;

		var textNodes = _getTextNodes(document);
		textNodes = $(textNodes);
		textNodes.parent().mouseup(_getSelectedText);
	}

	/**
		Code from http://stackoverflow.com/questions/439463/how-to-get-get-and-post-variables-with-jquery
	**/
	function _getQueryParams(qs) {
	    qs = qs.split("+").join(" ");
	    var params = {},
	        tokens,
	        re = /[?&]?([^=]+)=([^&]*)/g;

	    while (tokens = re.exec(qs)) {
	        params[decodeURIComponent(tokens[1])]
	            = decodeURIComponent(tokens[2]);
	    }

	    return params;
	}

	function _getTextNodes(node) {

		var whitespace = /^\s*$/;		
	    var textNodesStack = new Array();

	    function _getTextNodesAux(node) {
	        if (node.nodeType == 3) {
	            if (!whitespace.test(node.nodeValue)) {
	                textNodesStack.push(node);
	            }
	        } else {
	        	if (node.id != 'com-estudiocaravana-errata-boxWrapper' && node.childNodes != undefined){        		
	        		for(i in node.childNodes){
	        			_getTextNodesAux(node.childNodes[i]);
	        		}        		
	           	}
	        }
	    }

	    _getTextNodesAux(node);
	    return textNodesStack;
	}

	function _getSelectedText(event)
	{
		var sel = _getSelection();
					
		var text = sel + "";			
		
		if (text.length > 0){
			clearTimeout(_timeout);

			_selectedRange = sel.getRangeAt(0);									

			_showBox(event);				
			var errata = $(_nsid+'errata');			
			errata.html(text);
		}
		else{
			hideBox();
		}		
	}

	function _getSelection(){
		
		if (window.getSelection)
		{
			sel = window.getSelection();
		}
		else if (document.getSelection)
		{
			sel = document.getSelection();
		}
		else if (document.selection)
		{
			sel = document.selection.createRange().text;
		}

		return sel;
	}

	function sendErrata(){
		
		//Error checking
		var errata = $.trim($(_nsid+"errata").text());
		var correction = $.trim($(_nsid+"correction").val());
		var email = $.trim($(_nsid+"email").val());

		var hasErrors = false;

		console.log("errata.length = "+errata.length);
		console.log("correction.length = "+correction.length);

		//Error handling
		if (errata.length == 0){
			$(_nsid+"errata-error-noerrata").show();
			hasErrors = true;
		}
		if (correction.length == 0){
			$(_nsid+"correction-error-nocorrection").show();
			hasErrors = true;	
		}
		if (email.length != 0){
			if (!/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i.test(email)){
				$(_nsid+"email-error-invalidformat").show();
				hasErrors=true;
			}
		}
		

		if (!hasErrors){

			$(_nsid+"title").hide();
			$(_nsid+"form").hide();
			_setStatus("sendingErrata",false);

			var url = _rootDirPath+"/view/newErrata.php";
			
			var path = escape(
						_getElementPath(_selectedRange.startContainer)+'/'+_letterToken+'_'+_selectedRange.startOffset+"->"+
						_getElementPath(_selectedRange.endContainer)+'/'+_letterToken+'_'+_selectedRange.endOffset
					   );

			//We wrap the errata with a div in order to make its identification easier 
			var errataWrapper = document.createElement("div");
			errataWrapper.id = _ns+"errataWrapper";
			_selectedRange.surroundContents(errataWrapper);
			
			/**
			TODO Should we clone the whole HTML or just the BODY? 
			The HTML may include code we don't need such as the errataBox, 
			but showing the errata to the webmaster in its original context 
			(style, scripts, etc.) could be a cool feature.
			**/
			
			var html = $("<div />").append($("body").clone()).html();	
			
			errataWrapper = $("#"+errataWrapper.id);
			errataWrapper.contents().unwrap();

			var ip = $(_nsid+"ipAddress").val();
			
			var data = "errata="+encodeURIComponent(errata)
						+"&correction="+encodeURIComponent(correction)
						+"&url="+encodeURIComponent(document.URL)
						+"&path="+encodeURIComponent(path)
						+"&ip="+encodeURIComponent(ip)
						+"&html="+encodeURIComponent(html);

			var postID = $(_nsid+"postID").val();
			if (postID){
				data+="&postID="+encodeURIComponent(postID);
			}
			
			console.log("Sent message: "+data);	

			$.ajax({
				url: url,
				type: "POST",
				data: data
			}).done(function(msg){
				console.log("Returned message: '" + msg +"'");
				_setStatus("errataSent",true);
				_timeout = setTimeout(hideBox,3000);
			});

		}
	}

	function _setStatus(status,allowMouseEvent){
		_allowMouseEvent = allowMouseEvent;		 
		$(_nsid+"status").children().hide(1,function(){
			if (status != undefined && status.length > 0){
				var idStatus = _nsid+"status-"+status;				
				$(idStatus).show();			
			}
		});
	}

	function _getElementPath(element)
	{
		return "/" + $(element).parents().andSelf().map(function() {
			var $this = $(this),
				$parent = $this.parent(),
				tagName = this.nodeName,
				nodePosition = 0;

	        if (tagName != undefined && $parent){
	        	nodePosition = $parent.contents().filter(function(){ return this.nodeName == tagName; }).index(this);
	        }
	        
	        tagName += "_" + nodePosition;	        
	        
	        return tagName;

	    }).get().join("/");	    
	}

	function _highlightErrata(root,path,className){
		var pathElement,
			tagName,
			textContent,
			elementPosition;

		var $element = $(root);		

		path = path.split("/");

		for (p in path){
			if (!path[p]) continue;

			pathElement = path[p].split("_");
			tagName = pathElement[0];
			elementPosition = pathElement[1] ? pathElement[1] : 0;

			if (tagName == _letterToken){
				textContent = $element.text();
				$element.replaceWith( textContent.slice(0,elementPosition) + '<span class="' + className + '"></span>' + textContent.slice(elementPosition,textContent.length) );

				return true;
			}
			else{
				$element = $($element.contents().filter(function(){ return this.nodeName == tagName; }).get(elementPosition));
			}

			if (!$element) return false;
		}

		return false;
		
	}
	
	function _showBox(event){

		hideBox();

		$(_nsid+"title").show();
		$(_nsid+"boxWrapper")
				.css("left",event.pageX)
				.css("top",event.pageY)
				.show();		
	}
	
	function hideBox(){
		if (_allowMouseEvent){				
			$(_nsid+"form").hide();
			$(_nsid+"boxWrapper").hide();					
			$(_nsid+"correction").val("");
			$(_nsid+"status").children().hide();
			_allowMouseEvent = true;
		}
	}
	
	function showForm(){		
		$("."+_ns+"error").hide();
		$(_nsid+'form').show();
	}
	
	function showDetails(){
		$(_nsid+'details').show();
	}	
	
	var ns = com.estudiocaravana.Errata;

	//Public methods declaration
	ns.showForm = showForm;
	ns.showDetails = showDetails;
	ns.sendErrata = sendErrata;
	ns.init = init;
	
})();

var errata;

$(function(){
	
	errata = com.estudiocaravana.Errata;
	errata.init();
	
});


