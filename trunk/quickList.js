function attachQuickList(textBox, list, searchString, onselect) {

	textBox.quickList = list.childNodes;
	textBox.searchString = searchString;
	textBox.onkeyup = resortList;
	textBox.onSelectFunction = onselect;
	textBox.originalValue = true;
	textBox.onfocus = function() { if(this.originalValue) this.value = ""; this.originalValue = false; }
}

function resortList(e) {
	if(e!=null && e.keyCode==13) {
		for(var index=0; index<this.quickList.length; index++) {
			if(this.quickList[index].className == "active first") {
				this.onSelectFunction(this.quickList[index].innerHTML,this.quickList[index].getAttribute("data"));
				break;
			}
		}
	}
	else if(e!=null && e.keyCode==38){ //up
		for(var index=0; index<this.quickList.length; index++) {
			if(this.quickList[index].className == "active first") {
				if(index > 0) {
					this.quickList[index].className = this.quickList[index].className.replace(/first/,"");
					for(var subIndex=index-1; subIndex>=0; subIndex--)
						if(this.quickList[subIndex].className == "active") {
							this.quickList[subIndex].className = "active first";
							break;
						}
				}
				break;
			}
		}
	}
	else if(e!=null && e.keyCode==40){ //down
		for(var index=0; index<this.quickList.length; index++) {
			if(this.quickList[index].className == "active first") {
				if(index < this.quickList.length-1) {
					this.quickList[index].className = this.quickList[index].className.replace(/first/,"");
					for(var subIndex=index+1; subIndex<this.quickList.length; subIndex++)
						if(this.quickList[subIndex].className == "active") {
							this.quickList[subIndex].className = "active first";
							break;
						}
				}
				break;
			}
		}
	}
	else {
		var filterRegex = eval(this.searchString.replace(/%1/g,slashify(this.value)));
		var firstFound = false;
		for(var index=0; index<this.quickList.length; index++) {
			if(filterRegex.test(this.quickList[index].innerHTML)) {
				this.quickList[index].className = "active";
				if(!firstFound) {
					this.quickList[index].className = "active first";
					firstFound = true;
				}
			}
			else {
				this.quickList[index].className = "inactive";
			}
		}
	}
}

function listClick(item) {
	if(item.className == "active first") return;
	for(var index=0; index<item.parentNode.childNodes.length; index++) 
		if(item.parentNode.childNodes[index].className == "active first" && item.parentNode.childNodes[index]!=item) {
			item.parentNode.childNodes[index].className = "active";
			break;
		}
	item.className="active first";
	
}

function slashify(string) {
	string = string.replace(/\!/g,"\\!");
	string = string.replace(/\$/g,"\\$");
	string = string.replace(/\(/g,"\\(");
	string = string.replace(/\)/g,"\\)");
	string = string.replace(/\*/g,"\\*");
	string = string.replace(/\+/g,"\\+");
	string = string.replace(/\./g,"\\.");
	string = string.replace(/\//g,"\\/");
	string = string.replace(/\:/g,"\\:");
	string = string.replace(/\=/g,"\\=");
	string = string.replace(/\?/g,"\\?");
	string = string.replace(/\[/g,"\\[");
	string = string.replace(/\]/g,"\\]");
	string = string.replace(/\^/g,"\\^");
	string = string.replace(/\{/g,"\\{");
	string = string.replace(/\}/g,"\\}");
	string = string.replace(/\|/g,"\\|");
	string = string.replace(/\#/g,"\\#");
	string = string.replace(/\%/g,"\\%");
	string = string.replace(/\&/g,"\\&");
	string = string.replace(/\,/g,"\\,");
	string = string.replace(/\-/g,"\\-");
	string = string.replace(/\;/g,"\\;");
	string = string.replace(/\</g,"\\<");
	string = string.replace(/\>/g,"\\>");
	string = string.replace(/\@/g,"\\@");
	string = string.replace(/\_/g,"\\_");
	string = string.replace(/\~/g,"\\~");
	string = string.replace(/\"/g,"\\\"");
	string = string.replace(/\'/g,"\\'");
	return string;
}
