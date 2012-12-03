/*javascript for Bubble Tooltips by Alessandro Fulciniti
- http://pro.html.it - http://web-graphics.com 
The script can be called by: 
enableTooltips('content','input'); or enableTooltips(null,'input');
*/
/*function enableTooltips(id){*/
function enableTooltips(path, id, tag){
var links,i,h;
if(!document.getElementById || !document.getElementsByTagName) return;
AddCss(path);
h=document.createElement("span");
h.id="btc";
h.setAttribute("id","btc");
h.style.position="absolute";
document.getElementsByTagName("body")[0].appendChild(h);
/*if(id==null) links=document.getElementsByTagName("a");
else links=document.getElementById(id).getElementsByTagName("a");*/
/* Fix by WalleniuM*/
if(id==null) {
   if(tag==null) links=document.getElementsByTagName("a");
   else links=document.getElementsByTagName(tag);}
   else {if(tag==null) links=document.getElementById(id).getElementsByTagName("a");
   else    links=document.getElementById(id).getElementsByTagName(tag);}
/* End of fix*/
for(i=0;i<links.length;i++){
    Prepare(links[i]);
    }
}

function Prepare(el){
var tooltip,t,b,s,l;
t=el.getAttribute("title");
/* Fix by WalleniuM
if(t==null || t.length==0) t="link:";*/
if(t==null || t.length==0) return; 
/* End of fix*/
el.removeAttribute("title");
tooltip=CreateEl("span","tooltip");
s=CreateEl("span","top");
/*s.appendChild(document.createTextNode(t));
  fix by WalleniuM */
var oDiv=document.createElement("DIV");
s.appendChild(oDiv);
oDiv.innerHTML = t;
/*end of fix*/
tooltip.appendChild(s);
b=CreateEl("b","bottom");
/* Fix by Simon Wallmann
l=el.getAttribute("href");
if(l.length>30) l=l.substr(0,27)+"...";
b.appendChild(document.createTextNode(l));
end of Fix
*/
tooltip.appendChild(b);
setOpacity(tooltip);
el.tooltip=tooltip;
el.onmouseover=showTooltip;
el.onmouseout=hideTooltip;
el.onmousemove=Locate;
}

function showTooltip(e){
document.getElementById("btc").appendChild(this.tooltip);
Locate(e);
}

function hideTooltip(e){
var d=document.getElementById("btc");
if(d.childNodes.length>0) d.removeChild(d.firstChild);
}

function setOpacity(el){
el.style.filter="alpha(opacity:95)";
el.style.KHTMLOpacity="0.95";
el.style.MozOpacity="0.95";
el.style.opacity="0.95";
}

function CreateEl(t,c){
var x=document.createElement(t);
x.className=c;
x.style.display="block";
return(x);
}

function AddCss(path){
var l=CreateEl("link");
l.setAttribute("type","text/css");
l.setAttribute("rel","stylesheet");
l.setAttribute("href",path+"bt.css");
l.setAttribute("media","screen");
document.getElementsByTagName("head")[0].appendChild(l);
}

function Locate(e){
var posx=0,posy=0;
if(e==null) e=window.event;
if(e.pageX || e.pageY){
    posx=e.pageX; posy=e.pageY;
    }
else if(e.clientX || e.clientY){
    if(document.documentElement.scrollTop){
        posx=e.clientX+document.documentElement.scrollLeft;
        posy=e.clientY+document.documentElement.scrollTop;
        }
    else{
        posx=e.clientX+document.body.scrollLeft;
        posy=e.clientY+document.body.scrollTop;
        }
    }
document.getElementById("btc").style.top=(posy+10)+"px";
document.getElementById("btc").style.left=(posx-20)+"px";
}