var templateNum = 0
var timelineURI = window.bgidomain + "timeline.cgi"
window.timeline = []

var monthLabels = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]

var dayLabels = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]

var weekPieDataSets = {}
var monthPieDataSets = {}

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
	$('name[byweek]').click(function(){
		setupWeekData()
	})
	$('name[bymonth]').click(function(){
		setupMonthData()	
	})
	$('section').on('click','[name="itemcontainer"] button', function(){
		var items = $(this).parent().find('[name="items"]')
		items.slideToggle()
		$(this).text('Toggle Item Display')

	})
}
	
jQuery( document ).ready(function( $ ) {
	//use the timeline data since it's in the closest form we want to work with
	$.get(timelineURI, function(timeline){
		window.timeline = timeline
		setup()
	}).error(function(evt){
		console.error(evt)
		alert("Could not load your data! Try logging in again")
	})
})