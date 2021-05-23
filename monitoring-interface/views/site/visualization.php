<?php
use \app\models\VisualizationGraphState;
$views = VisualizationGraphState::getViews();
?>
<style type="text/css">
    .node {}

    .link { stroke: #999; stroke-opacity: .6; stroke-width: 1px; }
    .link_red { stroke: red;  stroke-dasharray: 5,5; stroke-opacity: .6; stroke-width: 1px; }
    .link_green { stroke: green;  stroke-dasharray: 5,5; stroke-opacity: .6; stroke-width: 1px; }
    .deleted { display: none;}
    .edgelabel { background-color: lightgrey;}
    .panel {width: 150px; height: 500px; padding:20px;}
    .right {float: right;}
    .left {float: left;}
    a:hover, a:visited, a:link, a:active {text-decoration: none !important;}
    #validation_errors { color: red;}
    #validation_error { color: red;}
    .highlighted {color:dodgerblue;}
</style>
<body>
<div class="left panel">
    Validation error(s):
    <div id="validation_errors"></div>
</div>
<svg  width="840" height="600"></svg>
<div class="right panel">
    <div id="settings">
        View:
        <select id="view_selector" onchange="viewSelectorChanged(this)">
            <?php
                foreach($views as $view) {
                    echo '<option value="'.$view->view_id.'">'.$view->name.'</option>';
                }
            ?>
        </select>
    </div>
    <br>
    <div id="information"></div>
</div>
<div id="last_update"></div>
<script src="https://d3js.org/d3.v4.min.js" type="text/javascript"></script>
<script src="https://d3js.org/d3-selection-multi.v1.js"></script>

<script type="text/javascript">
    var colors = d3.scaleOrdinal(d3.schemeCategory10);

    var svg = d3.select("svg"),
        width = +svg.attr("width"),
        height = +svg.attr("height"),
        node,
        edgepaths,
        edgelabels,
        link;

    const graphUrl = "http://127.0.0.1:8000/index.php?r=api/get-graph-data";
    const validationUrl = "http://127.0.0.1:8000/index.php?r=api/get-validation-errors";
    const viewRuleUrl = "http://127.0.0.1:8000/index.php?r=validation%2Fview";
    var simulation = d3.forceSimulation()
        .force("link", d3.forceLink().id(function (d) {return d.id;}).distance(150).strength(1))
        .force("charge", d3.forceManyBody())
        .force("center", d3.forceCenter(width / 2, height / 2));

    function viewSelectorChanged(caller) {
        getData(graphUrl+"&id="+caller.value);
    }
    getData(graphUrl);
    updateValidationPanel(validationUrl);
    setInterval(function(){updateValidationPanel(validationUrl);},2000);
    function getData(url) {
        d3.json(url, function (error, data) {

            if (error) {
                alert('error');
                console.log(error);
                throw error;
            }
            svg.selectAll("*").remove();            
            $("#last_update").html("Last graph update: "+data.updated_at);
            update(data.graph.links, data.graph.nodes);
        });
    }

    function updateValidationPanel(url) {
        d3.json(url, function (error, data) {
            if (error) {
                alert('error');
                console.log(error);
                throw error;
            }
            var html = "<ul>";
            if(data !== undefined && data.validation_errors !== undefined && data.validation_errors.length !== 0) {
                data.validation_errors.forEach(function(item){
                   html += "<li><a href='"+viewRuleUrl+"&id="+item.id+"' id='validation_error' target='_blank'>"+item.name+"</a></li>";
                });
            }
            else {
                html +="<span style='color:black'>-</span>";
            }
            html+="</ul>";
            $("#validation_errors").html(html);
        });
    }

    function update(links, nodes) {
        if(links !== undefined) {
            // 2 passes are required because
            // there are 3 link type
            // 1 pass only deals with one
            for(var i = 0; i<2; i++) {
                let edgesToDelete = [];
                // checks if two edges have the same
                // source and target and select the lower priority ones
                links.forEach(function (a) {
                    edgesToDelete.push(links.find(b =>
                        a.source === b.source &&
                        a.target === b.target &&
                        a !== b &&
                        a.priority < b.priority));
                });
                let updatedLinks = [];
                for (let i = 0; i < links.length; i++) {
                    // if the edge is not deleted
                    if (!edgesToDelete.includes(links[i])) {
                        updatedLinks.push(links[i]);
                    }
                }
                links = updatedLinks;
            }
        } 
        else {
            links = [];
        }
        if(nodes === undefined) {
            noedes = [];
        }
        svg.append('defs')
            .append('marker')
            .attrs({
                'id': 'arrowhead',
                'refX': 13,
                'refY': 0,
                'orient': 'auto',
                'markerWidth': 12,
                'markerHeight': 12,
                'viewBox': '-0 -5 10 10',
            })
            .append('svg:path')
            .attr('d', 'M 0,-5 L 10 ,0 L 0,5')
            .attr('fill', 'grey');
        link = svg.selectAll(".link")
            .data(links)
            .enter()
            .append("line")
            .attr("class", function (d) {
                if (d.deleted) return "deleted";
                if(d.type == 'COULD_CONSUME') {
                    return 'link_red'
                }
                else if(d.type=='AUTHORIZED') {
                    return 'link_green';
                }
                else {
                    return 'link';
                }
            })
            .attr('marker-end', 'url(#arrowhead)');

        link.append("title")
            .text(function (d) {
                return d.type;
            });

        edgepaths = svg.selectAll(".edgepath")
            .data(links)
            .enter()
            .append('path')
            .attrs({
                'id': function (d, i) {
                    return 'edgepath' + i
                },
                'class': 'edgepath',
                'fill-opacity': 0,
                'stroke-opacity': 0,
            });

        edgelabels = svg.selectAll(".edgelabel")
            .data(links)
            .enter()
            .append('text')

            .attrs({
                'class': 'edgelabel',
                'id': function (d, i) {
                    return 'edgelabel' + i
                },
                'font-size': 10,
                'fill': '#aaa'
            });

        edgelabels.append('textPath')
            .attr('xlink:href', function (d, i) {
                return '#edgepath' + i
            })
            .style("text-anchor", "middle")
            .attr("startOffset", "50%")
            .text(function (d) {
                return d.type
            })
            .on("mouseover", function (e) {
                let properties = " - Service Definition: " +
                    '<span class="highlighted">'+e.service_definition + "</span><br> - Interface: " +
                    '<span class="highlighted">'+e.interface + "</span><br> - Service URI: " +
                    '<span class="highlighted">'+e.service_uri+ "</span><br> - Orchestration priority: " +
                    '<span class="highlighted">'+e.orchestration_priority+"</span>";
                $("#information").html(properties);
            })
            .on("mouseout", function (e) {
                $("#information").html("");
            });

        node = svg.selectAll(".node")
            .data(nodes)
            .enter()
            .append("g")
            .attr("class", "node")
            .call(d3.drag()
                    .on("start", dragstarted)
                    .on("drag", dragged)
            );

        node.append("circle")
            .attr("r", 5)
            .style("fill", function (d, i) {
                return colors(i);
            })

        node.append("title")
            .text(function (n) {
                return n.id;
            });

        node.append("text")
            .attr("dy", -3)
            .html(function (n) {
                return n.name + ": " + n.label;
            })
            .on("mouseover", function (n) {
                let properties = '-ID: <span class="highlighted">' + n.id +'</span>' +
                    '<br>Address: <span class="highlighted">' + n.address + '</span>'+
                    '<br>Port: <span class="highlighted">' + n.port+'</span>';
                $("#information").html(properties);
            })
            .on("mouseout", function (n) {
                $("#information").html("");
            });

        simulation
            .nodes(nodes)
            .on("tick", ticked);
        simulation.force("link")
            .links(links);
    }

    function ticked() {
        link
            .attr("x1", function (d) {return d.source.x;})
            .attr("y1", function (d) {return d.source.y;})
            .attr("x2", function (d) {return d.target.x;})
            .attr("y2", function (d) {return d.target.y;});

        node
            .attr("transform", function (d) {return "translate(" + d.x + ", " + d.y + ")";});

        edgepaths.attr('d', function (d) {
            return 'M ' + d.source.x + ' ' + d.source.y + ' L ' + d.target.x + ' ' + d.target.y;
       });

        edgelabels.attr('transform', function (d) {
            if (d.target.x < d.source.x) {
                var boundaryBox = this.getBBox();

                rx = boundaryBox.x + boundaryBox.width / 2;
                ry = boundaryBox.y + boundaryBox.height / 2;
                return 'rotate(180 ' + rx + ' ' + ry + ')';
            }
            else {
                return 'rotate(0)';
            }
        });
    }

    function dragstarted(d) {
        if (!d3.event.active) simulation.alphaTarget(0.3).restart()
        d.fx = d.x;
        d.fy = d.y;
    }

    function dragged(d) {
        d.fx = d3.event.x;
        d.fy = d3.event.y;
    }
</script>