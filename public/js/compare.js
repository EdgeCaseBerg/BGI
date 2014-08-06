var templateNum = 0
window.timeline = []

var monthLabels = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]

var dayLabels = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]

var weekPieDataSets = []
weekPieDataSets = []

//monthPieData holds data for each dataset, indexed by month
var monthPieData = {}
var monthPieDataSets = []
for (var i = 0; i < monthLabels.length; i++) {
	monthPieDataSets.push([])	
};

function clearTemplates(){
	$('.compareItem:not(#template):not(#templateWeek)').remove()
}

function showTemplates(){
	$('.compareItem:not(#template):not(#templateWeek').fadeIn()
}


function makeNewTemplate(){
	templateNum++
	cpy = $('#template').clone()
	cpy.attr('id', 'cpy' + templateNum)
	return cpy;
}

function makeNewWeekTemplate(){
	templateNum++
	cpy = $('#templateWeek').clone()
	cpy.attr('id', 'cpy' + templateNum)
	return cpy;
}


function setupMonthData(){
	templateNum = 0;
	clearTemplates()
	for (var i = 0; i < monthLabels.length; i++) {
		var lbl = monthLabels[i]
		tmpl = makeNewTemplate()
		tmpl.find('[name=title]').text(lbl)
		//Use dataset to populate pie and items list
		var pieCanvas = tmpl.find('[name=pie]')[0].getContext("2d")
		var chart = new Chart(pieCanvas).Pie(monthPieDataSets[i], {})
		//populate item list:
		$('section').append(tmpl)
		tmpl.fadeIn().css('display','')
	};
	showTemplates()
	
}

function setupWeekData(){
	templateNum = 0;
	clearTemplates()
	//compute week by week for each item
	showTemplates()
}

function setup(){
	for (account in window.timeline) {
		//timeline is by account
		var account = window.timeline[account]
		var color = n2c(account.name)
		var accountTotals = [0,0,0,0,0,0,0,0,0,0,0,0]
		for (var i = account.items.length - 1; i >= 0; i--) {
			var item = account.items[i]
			var date = new Date(item.date*1000)
			//monthPieData[date.getMonth()].push(item)
			accountTotals[date.getMonth()] += item.amount
		};
		for (var i = accountTotals.length - 1; i >= 0; i--) {
			var accountTotal = accountTotals[i]
			monthPieDataSets[i].push({
				value: accountTotal,
				color: color,
				highlight: n2c(account.name + "a"),
				label: account.name
			})
		};
	};
	console.log(monthPieDataSets)
	$('[name=byweek]').click(function(){
		setupWeekData()
	})
	$('[name=bymonth]').click(function(){
		setupMonthData()	
	})
	$('section').on('click','[name="itemcontainer"] button', function(){
		var items = $(this).parent().find('[name="items"]')
		items.slideToggle()
		$(this).text('Toggle Item Display')

	})
}
	
jQuery( document ).ready(function( $ ) {
	var timelineURI = window.bgidomain + "timeline.cgi"
	//use the timeline data since it's in the closest form we want to work with
	$.get(timelineURI, function(timeline){
		window.timeline = timeline
		$('marquee').fadeOut()
		setup()
	}).error(function(evt){
		alert("Could not load your data! Try logging in again")
		console.error(evt)
		
	})
})