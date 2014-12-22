$(document).ready(function(){
	var c200Width = 200 //surprise
	var c200Height = 200
	var c200Radius = 100
	color = d3.scale.category10()

	/* Pie Chart */
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


    /* Goal horizontal bar charts  */
    c300Data = window.weekly300.goals
    c300Width = 500
    c300Height = 40
    var chart300 = d3.selectAll("#chart-area-300")
        .append("div")
            .attr("class","horizontal")

    var bars = chart300.selectAll("div")
            .data(c300Data)
            .enter()
                .append("svg:svg")
                    .attr("class", "horizontal")
                    .attr("width", c300Width)
                    .attr("height", c300Height)
                    .attr("style", "clear: both; float: left;")
                    .attr("rel", function(d) { return d.goal_id })


    for (idx in c300Data) {
        var obj = c300Data[idx]
        scale = d3.scale.linear()
            .domain([0, +obj.goal_amount])
            .range([0, c300Width])

        var svg = d3.select("svg.horizontal[rel=\""+obj.goal_id+"\"]").selectAll("g")
                .data(obj.accounts)

        svg.enter()
            .append("svg:rect")
            .attr("width", function(d,i) { 
                return scale(+d) 
            })
            .attr("height", c300Height)
            .attr("fill", function(d,i) { 
                return color(obj.accountsKeys[i]) 
            })
            .attr("x",function(d,i) {
                return i == 0 ? 0 : (scale(+obj.accounts[i-1]))
            }) 
            .attr("y",0)

    }
})