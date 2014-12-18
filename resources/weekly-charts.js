$(document).ready(function(){
	var c200Width = 200 //surprise
	var c200Height = 200
	var c200Radius = 100
	color = d3.scale.category20c()

	
	var chart200 = d3.select("#chart-area-200")
		.append("svg:svg")
		.data([window.weekly200])
			.attr("width", c200Width)
			.attr("height", c200Height)
		.append("svg:g")
			.attr("transform", "translate(" + c200Radius + "," + c200Radius + ")")

	var arc = d3.svg.arc() 
        .outerRadius(c200Radius);

    var pie = d3.layout.pie()
    	.value(function(d) { return +d.amount })

    var arcs = chart200.selectAll("g.slice")
    	.data(pie)
    	.enter()
    		.append("svg:g")
    			.attr("class","slice")

    	arcs.append("svg:path")
    		.attr("fill", function(d,i) { return color(+window.weekly200[i].account_id) })
    		.attr("d", arc)

    	arcs.append("svg:text")
    		.attr("transform", function(d) {
    			d.innerRadius = 0
    			d.outerRadius = c200Radius
    			return "translate(" + arc.centroid(d) + ")"
    		})
    		.attr("text-anchor", "middle")
    		.text(function(d,i) { return window.weekly200[i].name })


})