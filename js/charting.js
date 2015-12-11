var datasets ={};

function getData(provider, stat)
{
	if (datasets[provider]==null)
	{
		$.ajax({
			type: "GET",
			url: "../database/getSteamData.php",
			data: {provider: provider},
			dataType: "json"
		}).done(function (data)
		{
			datasets[provider] = data;
			handleStat(data, stat)

		}).fail(function (jqXHR, textStatus, errorThrown)
		{
			console.log("AJAX call failed: " + textStatus + ", " + errorThrown);
		});
	}
	else
	{
		handleStat(datasets[provider], stat);
	}
}

function handleStat(dataset, stat)
{
	switch (stat)
	{
		case "totalKills":
			var result = extractDateData(dataset, stat);
			createLineChart(result[0], [result[1]]);
			break;
		case "totalKillsVtotalDeaths":
			var resultKills = extractDateData(dataset, "totalKills");
			var resultDeaths = extractDateData(dataset, "totalDeaths");
			var input = [resultKills[1], resultDeaths[1]];
			createLineChart(resultKills[0], input);
			break;
		case "K/D":
			var resultKills = extractDateData(dataset, "totalKills");
			var resultDeaths = extractDateData(dataset, "totalDeaths");
			var deltaKills = getDelta(resultKills[1]);
			var deltaDeaths = getDelta(resultDeaths[1]);
			var label = resultKills[0];
			var resultKD = [];
			if (label.length > 0)
			{
				label.shift();
				for (var i = 0; i < deltaKills.length; i++)
				{
					resultKD.push(deltaKills[i] / deltaDeaths[i]);
				}
			}
			createLineChart(label, [resultKD]);
			break;
		case "deltaKills":
			var result = extractDateData(dataset, "totalKills");
			var deltaKills = getDelta(result[1]);
			if (result[0].length > 0)
			{
				result[0].shift();
			}
			createLineChart(result[0], [deltaKills]);
            break;
        case "totalMvps":
            var mvps = extractDateData(dataset, "totalMvps");
            drawData(mvps[1][mvps[1].length-1]);
            break;
        default:
            var newStat = stat.substr(0,3);
            var weapon = stat.substr(3,stat.length-1);
            switch(newStat){
                case "acc":
                    var totalshots = extractDateData(dataset,"total_shots_"+weapon);
                    var totalhits = extractDateData(dataset,"total_hits_"+weapon);
                    var acc = totalhits/totalshots;
                    drawData(acc);
                    break;

            }
	}


}

function getDelta(dataArray)
{
	var result = [];
	for (var i = 0; i < dataArray.length; i++)
	{
		if (i == 0)
		{
			continue;
		}
		result.push(dataArray[i] - dataArray[i-1])
	}
	return result;
}

function extractDateData(dataset, stat)
{
	var labels = [];
	var datapoints = [];
	for (var i of dataset)
	{
		var date = new Date(parseInt(i.TimeStamp) * 1000);
		labels.push(String(date.getDay()) + "/" + String(date.getMonth()) + " - " + String(date.getHours()) + ":" + String(date.getMinutes()));
		datapoints.push(parseInt(i[stat]));
	}
	return [labels, datapoints];
}

function createLineChart(labels, dataArrayArray)
{
	$("#canvas").remove();
	$('#canvasContainer').append('<canvas id="canvas" width="700" height="400"><canvas>'); //ChartJS is bugged. Need to destroy canvas completely to draw anew.
	$canvas = $("#canvas");
	context = $canvas.get(0).getContext("2d");
	if (labels.length == 0)
	{
		context.font = "30px Arial";
		context.fillText("Data Missing", 250, 100);
		return;
	}
	if (labels == 1)
	{
		labels.push(labels[0]);
	}

	dataset =[];
	for (var i = 0; i < dataArrayArray.length; i++)
	{
		if (dataArrayArray[i].length == 0)
		{
			context.font = "30px Arial";
			context.fillText("Data Missing", 250, 100);
			return;
		}
		if (dataArrayArray[i].length == 1)
		{
			dataArrayArray[i].push(dataArrayArray[i][0]);
		}
		var colour;
		switch (i)
		{
			case 0: colour = "rgba(0,0,220,0.2)";
				break;
			case 1: colour = "rgba(220,0,0,0.2)";
				break;
			case 2: colour = "rgba(0,220,0,0.2)";
				break;
			default: colour = "rgba("+String(Math.random()*255)+","+String(Math.random()*255)+","+String(Math.random()*255)+",0.2)";
		}
		var obj =
		{
			label: "Data",
			fillColor: colour,
			strokeColor: colour,
			pointColor: colour,
			pointStrokeColor: "#fff",
			pointHighlightFill: "#fff",
			pointHighlightStroke: colour,
			data: dataArrayArray[i]
		}
		dataset.push(obj);
	}
	var graphData =
	{
		labels: labels,
		datasets: dataset
	};
	context.clearRect(0,0, $canvas.width, $canvas.height);
	var chart = new Chart(context).Line(graphData, {bezierCurve: false});

}


function drawData(number) {
    $("#canvas").remove();
    $('#canvasContainer').append('<canvas id="canvas" width="700" height="400"><canvas>'); //ChartJS is bugged. Need to destroy canvas completely to draw anew.
    $canvas = $("#canvas");
    context = $canvas.get(0).getContext("2d");


    if(number == null){
        context.font = "30px Arial";
        context.fillText("Data Missing", 250, 100);
        return;

    }

    context.font = "30px Arial";
    context.fillText(number, 250, 100);



}
