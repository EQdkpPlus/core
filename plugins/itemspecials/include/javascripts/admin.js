/***********************************************
* Admin DIV Switcher
* (c) 2006 by Simon Wallmann
***********************************************/

function expand()
{
        var Nodes = document.getElementsByTagName("div")
        var max = Nodes.length
        for(var i = 0;i < max;i++) {
                var nodeObj = Nodes.item(i)
                var str = nodeObj.id
                if (str.match("section")) {
                        nodeObj.style.display = 'block';
                }
        }
}

function hideall()
{
        var Nodes = document.getElementsByTagName("div")
        var max = Nodes.length
        for(var i = 0;i < max;i++) {
                var nodeObj = Nodes.item(i)
                var str = nodeObj.id
                if (str.match("section")) {
                        nodeObj.style.display = 'none';
                }
        }
}

function show_section(e) {
    if (document.getElementById(e).style.display == 'none') {
        document.getElementById(e).style.display = 'block';
    } else {
        document.getElementById(e).style.display = 'none';
    }
}