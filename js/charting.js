
var context = $("#canvas").get(0).getContext("2d");

$.get("../database/getSteamData.php", function(data)
{
    if (data.length == 0)
    {
        context.font = "30px Arial";
        context.fillText("No Data Available", 100, 100);
        return;
    }
    var labels = [];
    var kills = [];
    var deaths= [];
    
    //PhpStorm will raise an error here, but it is a valid for statement and it compiles. Do not change "of" to "in"!
    for (var i of data)
    {
        labels.push(new Date(parseInt(i.TimeStamp)).getDate());
        kills.push(parseInt(i.totalKills));
        deaths.push(parseInt(i.totalDeaths));
    }
    console.log(labels);
    console.log(kills);
    console.log(deaths);

    var graphData =
    {
        labels: labels,
        datasets:
        [
            {
                label: "Kills",
                fillColor: "rgba(220,220,220,0.2)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: kills
            },
            {
                label: "Deaths",
                fillColor: "rgba(151,187,205,0.2)",
                strokeColor: "rgba(151,187,205,1)",
                pointColor: "rgba(151,187,205,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(151,187,205,1)",
                data: deaths
            }
        ]
    }
    var chart = new Chart(context).Line(graphData, {bezierCurve: false});
}, "json");


