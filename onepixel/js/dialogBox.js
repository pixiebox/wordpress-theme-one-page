;(function (root, factory) {
	if ( typeof define === 'function' && define.amd ) {
			define(factory);
	} else if ( typeof exports === 'object' ) {
			module.exports = factory;
	} else {
			root.dialogBox = factory(root);
	}
})(this, function (root) {
	// IE8 has no trim function
	if (typeof String.prototype.trim !== 'function') {
		String.prototype.trim = function () {
			return this.replace(/^\s+|\s+$/g, ''); 
		};
	}

	var api = {
		//
		// Methods
		//
		addEvent : function addEvent (evnt, el, func) {
			if (el.addEventListener) { // W3C DOM
				el.addEventListener(evnt,func,false);
			} else if (el.attachEvent) { // IE DOM
				el.attachEvent('on' + evnt, func);
			} else { // No much to do
				el[evnt] = func;
			}
		}
  , close : function close (evt) {
			(evt.preventDefault) ? evt.preventDefault() : evt.returnValue = false;

			var el = evt.srcElement || evt.target
				, elDialogBox = api.getClosest(el, '.dialogBox');

			if (elDialogBox !== false) {
				api.removeEvent('click', el, api.close);
				elDialogBox.parentElement.removeChild(elDialogBox);
			}
	}
	, delegateEvent: function delegateEvent (e) {
			var el = e.srcElement || e.target
				, dataCallback
				, dataRel;

			if (el instanceof HTMLAnchorElement) {} else if (el.parentNode instanceof HTMLAnchorElement) {
				(e.preventDefault) ? e.preventDefault() : e.returnValue = false;
				return el.parentNode.click();
			}

			dataCallback = el.getAttribute('data-callback');
			dataRel = el.getAttribute('data-rel');

			if (typeof dialogBox[dataRel] == 'function') {
				(e.preventDefault) ? e.preventDefault() : e.returnValue = false;
				dialogBox[dataRel](e);
			} else if (typeof window[dataCallback] == 'function'
			&& el.className.indexOf('dialog-answer') > -1) {
				var result = el.getAttribute('data-result');

				window[dataCallback](result);
				api.close(e);
			}
		}
	, forEach : function forEach (collection, callback, scope) {
			if (Object.prototype.toString.call(collection) === '[object Object]') {
				for (var prop in collection) {
					if (Object.prototype.hasOwnProperty.call(collection, prop)) {
						callback.call(scope, collection[prop], prop, collection);
					}
				}
			} else {
				for (var i = 0, len = collection.length; i < len; i++) {
					callback.call(scope, collection[i], i, collection);
				}
			}
		}
	, getClosest : function getClosest (elem, selector) {
			var firstChar = selector.charAt(0);

			// Get closest match
			for ( ; elem && elem !== document; elem = elem.parentNode ) {

				// If selector is a class
				if ( firstChar === '.' ) {
					if ( elem.className.indexOf( selector.substr(1) ) > -1 ) {
						return elem;
					}
				}

				// If selector is an ID
				if ( firstChar === '#' ) {
					if ( elem.id === selector.substr(1) ) {
						return elem;
					}
				} 

				// If selector is a data attribute
				if ( firstChar === '[' ) {
					if ( elem.hasAttribute( selector.substr(1, selector.length - 2) ) ) {
						return elem;
					}
				}

				// If selector is a tag
				if ( elem.tagName.toLowerCase() === selector ) {
					return elem;
				}

			}

			return false;
		}
	, invokeScript: function invokeScript (parentEl) {
			var scriptObj = parentEl.getElementsByTagName('SCRIPT')
			  , len = scriptObj.length;

			for (var i = 0; i < len; i++) {
				var scriptText = scriptObj[i].text
				  , scriptFile = scriptObj[i].src
				  , scriptTag = document.createElement('SCRIPT');

				if (scriptFile !== null && scriptFile !== '') {
					scriptTag.src = scriptFile;
				}

				scriptTag.text = scriptText;

				scriptObj[i].parentNode.removeChild(scriptObj[i]);
				document.body.appendChild(scriptTag);
			}
		}
	, pasteHTML: function pasteHTML (xhr, elDialogBox) {
			//ready?
			if (xhr.readyState != 4)
				return false;

			//get status:
			var status = xhr.status;

			//maybe not successful?
			if (status != 200) {
				console.log('AJAX: server status ' + status);
				return false;
			}

			elDialogBox.querySelector('.dialog-content').insertAdjacentHTML('beforeend', xhr.responseText);
			api.invokeScript(elDialogBox);
		}
	, promptOnSubmit : function promptOnSubmit (elDialogBox, postUrl, callback) {
			var form = elDialogBox.querySelector('form');

			api.addEvent('submit', form, function (e) {
				(e.preventDefault) ? e.preventDefault() : e.returnValue = false;

				if (postUrl !== null) {
					var xhr = new XMLHttpRequest();
					data = api.serialize(form, true);

					xhr.open("POST", postUrl, true);
					//Send the proper header information along with the request
					xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
					xhr.onreadystatechange = function () {
						//ready?
						if (xhr.readyState != 4)
							return false;

						//get status:
						var status = xhr.status;

						//maybe not successful?
						if (status != 200) {
							console.log('AJAX: server status ' + status);
							return false;
						}

						if (callback !== null) {
							if (typeof callback == 'function') {
								callback(this.responseText);
							} else if (typeof window[callback] == 'function') {
								window[callback](this.responseText);
							}
						}

						elDialogBox.parentElement.removeChild(elDialogBox);
					};

					xhr.send(data);
				} else if (callback !== null) {
					data = api.serialize(form, false);

					if (typeof callback == 'function') {
						callback(data);
					} else if (typeof window[callback] == 'function') {
						window[callback](data);
					}

					elDialogBox.parentElement.removeChild(elDialogBox);
				} else {
					form.submit();
				}
			});
		}
	, removeEvent : function removeEvent (evnt, el, func) {
			if (el.removeEventListener) { // W3C DOM
				el.removeEventListener(evnt,func,false);
			} else if (el.detachEvent) { // IE DOM
				el.detachEvent('on' + evnt, func);
			} else { // No much to do
				el.splice(evnt, 1);
			}
		}
	, serialize : function serialize (form, queryStr) {
			// https://code.google.com/p/form-serialize/
			if (!form || form.nodeName.toLowerCase() !== 'form') {
				return;
			}

			var i, j, q = [];

			for (i = form.elements.length - 1; i >= 0; i = i - 1) {
				if (form.elements[i].name === '') {
					continue;
				}

				switch (form.elements[i].nodeName.toLowerCase()) {
					case 'button':
						switch (form.elements[i].type) {
							case 'button':
							case 'reset':
							case 'submit':
								q.push(form.elements[i].name + '=' + encodeURIComponent(form.elements[i].value));
								break;
							}
						break;
					case 'input':
						switch (form.elements[i].type) {
							case 'button':
							case 'hidden':
							case 'reset':
							case 'password':
							case 'submit':
							case 'text':
								q.push(form.elements[i].name + '=' + encodeURIComponent(form.elements[i].value));
								break;
							case 'checkbox':
							case 'radio':
								if (form.elements[i].checked) {
									q.push(form.elements[i].name + '=' + encodeURIComponent(form.elements[i].value));
								}						
								break;
							case 'file':
								break;
							}
						break;			 
					case 'select':
						switch (form.elements[i].type) {
							case 'select-multiple':
								for (j = form.elements[i].options.length - 1; j >= 0; j = j - 1) {
									if (form.elements[i].options[j].selected) {
										q.push(form.elements[i].name + '=' + encodeURIComponent(form.elements[i].options[j].value));
									}
								}
								break;
							}
							case 'select-one':
								q.push(form.elements[i].name + '=' + encodeURIComponent(form.elements[i].value));
								break;
						break;
					case 'textarea':
						q.push(form.elements[i].name + '=' + encodeURIComponent(form.elements[i].value));
						break;
				}
			}
			return queryStr ? q.join('&') : q;
		}
	, setAttributes : function setAttributes (el, attrs, prefix) {
			if (typeof prefix !== 'string') prefix = '';

			for(var key in attrs) {
				el.setAttribute(prefix + key, attrs[key]);
			}
		}
	, sticky : function sticky (el) {
			var elInnerContent = el.querySelector('.dialog-content');

			el.className = el.className + ' stickyBox';
			elInnerContent.className = elInnerContent.className + ' clearfix';
		}
	  , tmplAjaxContainer : function (id) {
			return '<div aria-hidden="true" class="dialogBox" id="dialog-ajax-overlay-' + id + '" role="dialog"><div class="inner-dialog"><div class="dialog-content"><a class="close" href="#">&times;</a><div class="header"></div></div></div></div>';
		}
	  , tmplAlert : function (id) {
			return '<div aria-hidden="true" class="dialogBox" id="alert-overlay-' + id + '" role="dialog"><div class="inner-dialog"><div class="dialog-content"><a class="close" href="#">&times;</a><div class="header"></div><div class="dialog"></div></div></div></div></div>';  
		}
	  , tmplDialog : function tmplDialog (id) {
			return '<div aria-hidden="true" class="dialogBox" id="dialog-overlay-' + id + '" role="dialog"><div class="inner-dialog"><div class="dialog-content"><a class="close" href="#">&times;</a><div class="header"></div><div class="dialog"></div><div class="clearfix options"><button class="btn btn-default" data-callback="dialogCallback" data-answer="cancel">cancel</button><button class="btn btn-primary" data-callback="dialogCallback" data-answer="confirm">confirm</button></div></div></div></div>';
		}
	  , tmplPrompt : function tmplPrompt (id) {  
			return '<div aria-hidden="true" class="dialogBox" id="prompt-overlay-' + id + '" role="dialog"><div class="inner-dialog"><div class="dialog-content"><a class="close" href="#">&times;</a><div class="header"></div><div class="dialog"></div><form class="clearfix form-prompt" method="post" action=""><label for="answer">Answer</label><input class="form-control" id="answer" name="answer" type="text" /><button class="btn btn-primary" name="submit" type="submit">Submit</button></form></div></div></div>';
		}
	}
	, dialogBox = {
		alert : function alert (evt) {
			var el = evt.srcElement || evt.target
			  , id = document.querySelectorAll('.dialogBox').length + 1
			  , dialog
			  , elDialogBox
			  , sticky
			  , title
				, width;

			if (el) {
				(evt.preventDefault) ? evt.preventDefault() : evt.returnValue = false;

				dialog = el.getAttribute('data-dialog');
				sticky = el.getAttribute('data-sticky') !== null;
				title = el.getAttribute('data-title');
				width = el.getAttribute('data-width');
			} else {
				if ('selector' in evt) {
					evt['rel'] = 'alert';
					api.forEach(document.querySelectorAll(evt.selector), function (value, prop) {
						api.setAttributes(value, evt, 'data-');
					});

					return;
				}

				dialog = 'dialog' in evt ? evt.dialog : null;
				sticky = 'sticky' in evt;
				title = 'title' in evt ? evt.title : '';
				width = 'width' in evt ? evt.width : '';
			}

			document.body.insertAdjacentHTML('beforeend', api.tmplAlert(id));

			elDialogBox = document.querySelector('#alert-overlay-' + id);
			elTitle = elDialogBox.querySelector('.header');

			if (title !== null) {
				elTitle.innerHTML = title;
			} else {
				elTitle.parentElement.removeChild(elTitle);
			}

			if (dialog !== null) {
				elDialog = elDialogBox.querySelector('.dialog');
				elDialog.innerHTML = dialog;
			}

			if (width !== null) {
				elDialogContent = elDialogBox.querySelector('.dialog-content');
				elDialogContent.style.width = width;
			}

			api.addEvent('click', elDialogBox.querySelector('.close'), api.close);
			if (sticky) api.sticky(elDialogBox);
		}
	, dialog : function dialog (evt1) {
			var ajaxRequest = false
			  , el = evt1.srcElement || evt1.target
			  , id = document.querySelectorAll('.dialogBox').length + 1
			  , callback
			  , dialog
			  , elDialog
			  , elDialogBox
			  , elOptions
			  , elTitle
			  , html
			  , options
			  , requestURL
			  , sticky
			  , title
			  , width;

			if (el) {
				(evt1.preventDefault) ? evt1.preventDefault() : evt1.returnValue = false;

				requestURL = el.getAttribute('href');

				if (requestURL === null || requestURL == '#') {
					callback = el.getAttribute('data-callback');
					dialog = el.getAttribute('data-dialog');
					options = JSON.parse(el.getAttribute('data-options'));
					title = el.getAttribute('data-title');
				} else {
					ajaxRequest = true;
					requestURL = el.getAttribute('href');
				}

				sticky = el.getAttribute('data-sticky') !== null;
				width = el.getAttribute('data-width');
			} else {
				if ('selector' in evt1) {
					evt1['rel'] = 'dialog';
					api.forEach(document.querySelectorAll(evt.selector), function (value, prop) {
						api.setAttributes(value, evt1, 'data-');
					});

					return;
				}

				sticky = 'sticky' in evt1;
				title = 'title' in evt1 ? evt1.title : null;
				width = 'width' in evt1 ? evt1.width : null;

				if ('href' in evt1) {
					ajaxRequest = true;
					requestURL = evt1.href;
				} else {
					callback = 'callback' in evt1 ? evt1.callback : null;
					dialog = 'dialog' in evt1 ? evt1.dialog : null;
					options = 'options' in evt1 ? evt1.options : null;
				}
			}

			if (!ajaxRequest) {
				document.body.insertAdjacentHTML('beforeend', api.tmplDialog(id));

				elDialogBox = document.querySelector('#dialog-overlay-' + id);

				if (dialog !== null) {
					elDialog = elDialogBox.querySelector('.dialog');
					elDialog.innerHTML = dialog;
				}

				if (options !== null) {
					elOptions = elDialogBox.querySelector('.options');

					// remove standard buttons 
					while (elOptions.hasChildNodes()) {
						elOptions.removeChild(elOptions.lastChild);
					}

					api.forEach(options, function (value, prop) {
						elOptions.insertAdjacentHTML('beforeend', '<button class="dialog-answer ' + value.className + '" data-callback="' + callback + '" data-result="' + prop + '">' + prop + '</button>');
					});
				}

				if (width !== null) {
					elDialogContent = elDialogBox.querySelector('.dialog-content');
					elDialogContent.style.width = width;
				}
			} else {
				var xhr = new XMLHttpRequest();

				document.body.insertAdjacentHTML( 'beforeend', api.tmplAjaxContainer(id) );
				elDialogBox = document.querySelector('#dialog-ajax-overlay-' + id);

				xhr.open('GET', requestURL, true);
				xhr.onreadystatechange = function () {
					api.pasteHTML(this, elDialogBox);
				}
				xhr.send();
			}

			if (sticky) api.sticky(elDialogBox);

			elTitle = elDialogBox.querySelector('.header');

			if (title !== null) {
				elTitle.innerHTML = title;
			} else {
				elTitle.parentElement.removeChild(elTitle);
			}

			api.addEvent('click', elDialogBox.querySelector('.close'), api.close);			
		}
	, prompt : function prompt (evt) {
			var ajaxRequest = false
			  , dialog = null
			  , formData
			  , el = evt.srcElement || evt.target
			  , id = document.querySelectorAll('.dialogBox').length + 1
			  , elDialogBox
			  , form
			  , sticky
			  , title
			  , width;

			if (el) {
				(evt.preventDefault) ? evt.preventDefault() : evt.returnValue = false;
				requestURL = el.getAttribute('href');

				callback = el.getAttribute('data-callback');
				postUrl = el.getAttribute('data-postUrl');
				sticky = el.getAttribute('data-sticky') !== null;
				title = el.getAttribute('data-title');
				width = el.getAttribute('data-width');

				if (requestURL !== null && requestURL != '#') {
					ajaxRequest = true;
					requestURL = el.getAttribute('href');
				} else {
					dialog = el.getAttribute('data-dialog');
				}
			} else if (typeof evt == 'object') {
				if ('selector' in evt) {
					evt['rel'] = 'prompt';
					api.forEach(document.querySelectorAll(evt.selector), function (value, prop) {
						api.setAttributes(value, evt, 'data-');
					});

					return;
				}

				callback = 'callback' in evt ? evt.callback : null;
				postUrl = 'postUrl' in evt ? evt.postUrl : null;
				sticky = 'sticky' in evt;
				title = 'title' in evt ? evt.title : null;
				width = 'width' in evt ? evt.width : null;

				if ('href' in evt) {
					ajaxRequest = true;
					requestURL = evt.requestUrl;
				} else {
					dialog = 'label' in evt ? evt.label : null;
				}
			}

			if (!ajaxRequest) {
				document.body.insertAdjacentHTML('beforeend', api.tmplPrompt(id));
				elDialogBox = document.querySelector('#prompt-overlay-' + id);

				if (dialog !== null) elDialogBox.querySelector('label').innerText = dialog;

				api.promptOnSubmit(elDialogBox, postUrl, callback);
			} else {
				var xhr = new XMLHttpRequest();

				document.body.insertAdjacentHTML( 'beforeend', api.tmplAjaxContainer(id) );
				elDialogBox = document.querySelector('#dialog-ajax-overlay-' + id);

				xhr.open('GET', requestURL, true);
				xhr.onreadystatechange = function () {
					api.pasteHTML(this, elDialogBox);
					api.promptOnSubmit(elDialogBox, postUrl, callback);
				}
				xhr.send();
			}

			if (sticky) api.sticky(elDialogBox);

			elTitle = elDialogBox.querySelector('.header');

			if (title !== null) {
				elTitle.innerHTML = title;
			} else {
				elTitle.parentElement.removeChild(elTitle);
			}

			if (dialog !== null) {
				elDialog = elDialogBox.querySelector('.dialog');
				elDialog.innerHTML = dialog;
			}

			if (width !== null) {
				elDialogContent = elDialogBox.querySelector('.dialog-content');
				elDialogContent.style.width = width;
			}
	
			api.addEvent('click', elDialogBox.querySelector('.close'), api.close);
		}
	};

	api.addEvent('click', document, api.delegateEvent);

	return dialogBox;
});