<?php

/*
	-> http://code.google.com/apis/chart/#simple
	#by Corgan: Do some tweaks!!

	Name: GoogleGraphs Class
	Version: 1.0
	Description: A class to dynamically generate graphs from Google
	Author: Ryon Sherman
	Time: 12 hrs and 4 pots of coffee
	Note: I was very inebriated when I wrote this so if I fucked up or did
	something retarded. Forgive me, I'm no professional.
	Also, the code preview throws my tabs all
	out of whack.
	It looks good in my IDE, I swear!

*/

/**
 *	Description: Data object
 *	Methods:
 *	'addData'			=	Adds a unit of data to the graph
 *	Important Variables:
 *	'data'				=	Array of Zero (0.0) = 0, 1.0 = 1 and so on up to 100.0 = 100.
 *
 */
class Data
{
		var $data = array();

		/**
		 * 		Description: Adds a unit of data to the graph
		 *		Useage: $graph->Graph->addData(array('data', 'data', 'data', etc...);
		 *		Note: Current I only supported Text Encoding. I will delve into the Simple Encoding and Extended Encoding later
		 *		Arguments:
		 *		'data'		=	Zero (0.0) = 0, 1.0 = 1 and so on up to 100.0 = 100.
		 *
		 * @param unknown_type $data
		 */
		function addData($data = array())
		{
			if(!empty($data)) {
				$this->data[] = $data;
			}
		}
	}

	class Graph {
		/*
			Description: Graph object
			Methods:
				'Graoh'							=	Inititalizes object
				'setType'						=	Set the chart Type
				'setSubtype'					=
				'addShapeMarker'			=
				'setTitle'						=
				'setSize'						=
				'addLineStyle'				=
				'setAxisRange'				=
				'setGridLines'					=
				'setLegend'					=
				'addAxisLabel'				=
				'addAxisStyle'				=
				'addLabelPosition'			=
				'setLineColors'				=
				'setBarSize'					=
				'addFill'							=
			Important Variables:
				'FILL_PATTERNS'			=
				'TYPES'						=
				'LINE_SUBTYPES'			=
				'BAR_SUBTYPES'			=
				'PIE_SUBTYPES'			=
				'VENN_SUBTYPES'		=
				'SCATTER_SUBTYPES'	=
		*/
		var $FILL_PATTERNS = array('solid', 'gradient', 'stripes');
		var $TYPES = array('line', 'bar', 'pie', 'venn', 'scatter');
		var $LINE_SUBTYPES = array('chart', 'axis');
		var $BAR_SUBTYPES = array('horizontal_stacked', 'vertical_stacked', 'horizontal_grouped', 'vertical_grouped');
		var $PIE_SUBTYPES = array('2d', '3d');
		var $VENN_SUBTYPES = array('venn');
		var $SCATTER_SUBTYPES = array('scatter');

		var $type = 'line';
		var $subtype = 'chart';
		var $title = null;
		var $title_color = null;
		var $title_size = null;
		var $legend = array();
		var $line_colors = array();
		var $bar_size = null;
		var $chart_colors = array();
		var $chart_size = '300x300';
		var $axis = null;
		var $axis_labels = array();
		var $label_positions = array();
		var $axis_ranges = array();
		var $axis_styles = array();
		var $line_styles = array();
		var $grid_lines = null;
		var $markers = array();

		function setType($type = null) {
			/*
				Description: Set the chart Type
				Useage: $graph->Graph->setType('type');
				Arguments:
					'line'		=	A line chart, data points are spaced evenly along the x-axis.
									Provide a pair of data sets for each line you wish to draw,
									the first data set of each pair specifies the x-axis coordinates, the second the y-axis coordinates.
					'bar'		=	Horizontal and vertical bar chart respectively.
									Horizontal and vertical bar chart, respectively, in specified colors; multiple data sets are grouped.
									Bar chart size is handled in a different way than for other chart types.
					'pie'		=	Two dimensional pie chart.
									Three dimensional pie chart.
					'venn'	=
					'scatter'	=	Supply two data sets, the first data set specifies x coordinates, the second set specifies y coordinates.
			*/
			if(@in_array(strtolower($type), $this->TYPES)) {
				$this->type = strtolower($type);

				switch(strtolower($type)) {
					case 'line':
						$this->subtype = 'chart';
						break;
					case 'bar':
						$this->subtype = 'horizontal_grouped';
						break;
					case 'pie':
						$this->subtype = '2d';
						break;
					case 'venn':
						$this->subtype = 'venn';
						break;
					case 'scatter':
						$this->subtype = 'scatter';
						break;
				}
			}
		}

		function setSubtype($subtype = null) {
			/*
				Description: Set the chart Subtype
				Useage: $graph->Graph->setSubtype('subtype');
				Arguements:
					'line'
						'chart'						=	A line chart, data points are spaced evenly along the x-axis.
						'axis'							=	Provide a pair of data sets for each line you wish to draw,
															the first data set of each pair specifies the x-axis coordinates, the second the y-axis coordinates.
					'bar'
						'horizontal_stacked'
						'vertical_stacked'		=	Horizontal and vertical bar chart respectively.
						'horizontal_grouped'		=	Bar chart size is handled in a different way than for other chart types.
						'vertical_grouped'		=	Horizontal and vertical bar chart, respectively, in specified colors; multiple data sets are grouped.
					'pie'
						'2d'							=	Two dimensional pie chart.
						'3d'							=	Three dimensional pie chart.
					'venn'
					'scatter'							=	Supply two data sets, the first data set specifies x coordinates, the second set specifies y coordinates.
			*/
			switch($this->type) {
				case 'line':
					$subtypes = $this->LINE_SUBTYPES;
					break;
				case 'bar':
					$subtypes = $this->BAR_SUBTYPES;
					break;
				case 'pie':
					$subtypes = $this->PIE_SUBTYPES;
					break;
				case 'venn':
					$subtypes = $this->VENN_SUBTYPES;
					break;
				case 'scatter':
					$subtypes = $this->SCATTER_SUBTYPES;
					break;
				default:
					break;
			}
			if(in_array(strtolower($subtype), $subtypes))
				$this->subtype = strtolower($subtype);
		}

		function addShapeMarker($markers = array()) {
			/*
				Description: Specify shape markers for points on line charts and scatter plots
				Useage: $graph->Graph->addShapeMarker(array('shape', 'color', 'data set index', 'data point', 'size'));
				Arguments:
					'shape'
						'arrpw'						=	represents an arrow.
						'crpss'						=	represents a cross.
						'diamond'					=	represents a diamond.
						'circle'						=	represents a circle.
						'square'						=	represents a square.
						'small_vertical_line' 	=	represents a vertical line from the x-axis to the data point.
						'big_vertical_line'		=	represents a vertical line to the top of the chart.
						'horizontal_line'			=	represents a horizontal line across the chart.
						'x'								=	represents an x shape.
					'color'							=	Values are RRGGBB format hexadecimal numbers.
					'data set index'				=	the index of the line on which to draw the marker. This is 0 for the first data set, 1 for the second and so on.
					'data point'						=	Is a floating point value that specifies on which data point the marker will be drawn.
															This is 1 for the first data set, 2 for the second and so on. Specify a fraction to interpolate a marker between two points.
					'size'								=	is the size of the marker in pixels.
			*/
			if(!empty($markers)) {
				switch(strtolower($markers[0])) {
					case 'arrow':
						$markers[0] = 'a';
						$this->markers[] = $markers;
						break;
					case 'cross':
						$markers[0] = 'c';
						$this->markers[] = $markers;
						break;
					case 'diamond':
						$markers[0] = 'd';
						$this->markers[] = $markers;
						break;
					case 'circle':
						$markers[0] = 'o';
						$this->markers[] = $markers;
						break;
					case 'x':
						$markers[0] = 'x';
						$this->markers[] = $markers;
						break;
					case 'square':
						$markers[0] = 's';
						$this->markers[] = $markers;
						break;
					case 'small_vertical_line':
						$markers[0] ='v';
						$this->markers[] = $markers;
						break;
					case 'big_vertical_line':
						$markers[0] = 'V';
						$this->markers[] = $markers;
						break;
					case 'horizontal_line':
						$markers[0] = 'h';
						$this->markers[] = $markers;
						break;
					case 'horizontal_range':
						$markers[0] = 'r';
						$this->markers[] = $markers;
						break;
					case 'vertical_range':
						$markers[0] = 'R';
						$this->markers[] = $markers;
					default:
						break;
				}
			}
		}

		function setTitle($title, $color = null, $size = null) {
			/*
				Description: Specify a chart title
				Useage: $graph->Graph->setTitle('title', 'color', 'size');
				Arguments:
					'title'					=	Use a pipe character (|) to force a line break.
					'color'				=	Values are RRGGBB format hexadecimal numbers.
					'size'					=	Font size
			*/
			$this->title = str_replace(' ', '+', $title); //Replaces spaces with '+', use '|' for line breaks
			if(!empty($color))
				$this->title_color = str_replace('#', '', $color); //Strips '#' off color value
			if(!empty($size))
				$this->title_size = intval($size);
		}

		function setSize($width = null, $height = null) {
			/*
				Description:Specify chart size
				Useage: $graph->Graph->setSize('width', 'height');
				Note: The largest possible area for a chart is 300,000 pixels. As the maximum height or width is 1000 pixels,
						examples of maximum sizes are 1000x300, 300x1000, 600x500, 500x600, 800x375, and 375x800.
				Arguments:
					'width'			=	Size in pixels
					'height'			=	Size in pixels
			*/
			if(!empty($width) AND !empty($height)) {
				$this->chart_size = intval($width).'x'.intval($height);
			}
		}

		function addLineStyle($line_styles = array()) {
			/*
				Description: Specify chart line styles
				Useage: $graph->Graph->addLineStyle(array('line thickness', 'length of line', 'length of blank'));
				Notes:	Parameter values are floating point numbers, multiple line styles are separated by the pipe character (|).
							The first line style is applied to the first data set, the second style to the second data set, and so on.
			*/
			if(!empty($line_styles))
				$this->line_styles[] = $line_styles;
		}

		function setAxisRange($ranges = array()) {
			/*
				Description: Specify a range
				Useage: $graph->Graph->setAxisRange(array('axis index', 'start of range', 'end of range'));
			*/
			if(!empty($ranges))
				$this->axis_ranges = $ranges;
		}

		function setGridLines($x = 0, $y = 0, $line = 0, $blank = 0) {
			/*
				Description: Specify a chart grid
				Useage: $graph->Graph->setGridLines('x axis', 'y axis', 'length of line', 'length of blank');
				Notes: Parameter values can be integers or have a single decimal place - 10.0 or 10.5 for example.
			*/
			$this->grid_lines = null;
			if(!is_null($x)) $this->grid_lines .= $x.',';
			if(!is_null($y)) $this->grid_lines .= $y.',';
			if(!is_null($line)) $this->grid_lines .= $line.',';
			if(!is_null($blank)) $this->grid_lines .= $blank.',';
			$this->grid_lines = substr($this->grid_lines, 0, -1);
		}

		function setLegend($legend = array()) {
			/*
				Description: Specify a legend for a chart
				Useage: $graph->Graph->setLegend(array('label', 'label', 'label', etc...));
			*/
			$this->legend = $legend;
		}

		function addAxisLabel($axis_labels = array()) {
			/*
				Description: Specify labels
				Useage: $graph->Graph->addAxisLabel(array('label', 'label', 'label', etc...));
				Arguments:
					'label'				=	The first label is placed at the start, the last at the end, others are uniformly spaced in between.
			*/
			if(!empty($axis_labels))
				$this->axis_labels[] = $axis_labels;
		}

		function addAxisStyle($axis_styles = array()) {
			/*
				Description: Specify font size, color, and alignment for axis labels
				Useage: $graph->Graph->addAxisStyle(array('axis index', 'color', ['font size'], ['alignment']));
				Arguments:
					'axis index'			=	the axis index as specified
					'color'					=	the axis index as specified
					'font size'				=	is optional. If used this specifies the size in pixels.
					'alignment				=	is optional. By default: x-axis labels are centered,
													left y-axis labels are right aligned, right y-axis labels are left aligned.
													To specify alignment, use 0 for centered, -1 for left aligned, and 1 for right aligned.
			*/
			if(!empty($axis_styles))
				$this->axis_styles[] = $axis_styles;
		}

		function setAxis($axes = array()) {
			/*
				Description: Specify multiple axes
				Useage: $graph->Graph->setAxis(['bottom x-axis'], ['top x-axis'], ['left y-axis'], ['right y-axis']);
				Notes: Axes are specified by the index they have in the chxt parameter specification.
							The first axis has an index of 0, the second has an index of 1, and so on. You can specify multiple axes by including x, t, y, or r multiple times.
			*/
			if(!empty($axes)) {
				$this->axis = null;
				foreach($axes as $axis) {
					$this->axis .= $axis.',';
				}
				$this->axis = substr($this->axis, 0, -1);
			} else
				$this->axis = 'x,t,y,r';
		}

		function addLabelPosition($label_positions = array()) {
			/*
				Description: Specify label positions
				Useage: $graph->Graph->addLabelPosition(array('position', 'position', 'position', etc...));
				Arguments:
					'position'				=	Use floating point numbers for position values.
			*/
			if(!empty($label_positions))
				$this->label_positions[] = $label_positions;
		}

		function setLineColors($line_colors = array()) {
			/*
				Description: Specify colors for lines, bars, Venn diagrams, and pie segments
				Useage: $graph->Graph->setLineColors(array('color', 'color', 'color', etc...));
				Arguments:
					'color	=	Values are RRGGBB format hexadecimal numbers.
			*/
			$colors = array();
			foreach($line_colors as $color) {
				$colors[] = str_replace('#', '', $color);
			}
			$this->line_colors = $colors;
		}

		function setBarSize($size = null) {
			/*
				Description: specify bar thickness
				Useage: $graph->Graph->setBarSize('size');
				Arguments:
					'size'			=	Integer
			*/
			$this->bar_size = intval($size);
		}

		function addFill($area, $color, $pattern, $color2 = null, $angle = null, $var1 = null, $var2 = null ) {
			/*
				Description: Specify background fill or chart area
				Useage: $graph->Graph->addFill('area', 'color', 'pattern', ['color2'], ['angle'], ['var1'], ['var2']);
				Arguments:
					'area'
						'background'	=	Background fill area.
						'chart'			=	Chart fill area.
					''color'				=	Values are RRGGBB format hexadecimal numbers.
					'pattern'
						'solid'			=	Solid fill
						'gradient'		=	Gradient fill
						'stripes'			=	Striped fill
					'color2'				=	Values are RRGGBB format hexadecimal numbers.
					'angle'				=	Specifies the angle of the gradient between 0 (horizontal) and 90 (vertical).
					'var1'
						'offset'			=	specify at what point the color is pure where: 0 specifies the right-most chart position and 1 the left-most.
						'width'			= 	must be between 0 and 1 where 1 is the full width of the chart. Stripes are repeated until the chart is filled.
					'var2'
						'offset1'		=	specify at what point the color is pure where: 0 specifies the right-most chart position and 1 the left-most.
						'width'			=	must be between 0 and 1 where 1 is the full width of the chart. Stripes are repeated until the chart is filled.
			*/
			switch(strtolower($area)) {
				case 'background':
					$this->chart_colors['background']['color'] = str_replace('#', '', $color);
					if(in_array(strtolower($pattern), $this->FILL_PATTERNS)) {
						if(strtolower($pattern) == 'gradient') {
							$this->chart_colors['background']['pattern'] = strtolower($pattern);
							$this->chart_colors['background']['angle'] = $angle;
							$this->chart_colors['background']['color2'] = str_replace('#', '', $color2);
							$this->chart_colors['background']['offset'] = $var1;
							$this->chart_colors['background']['offset2'] = $var2;
						} elseif(strtolower($pattern) == 'stripes') {
							$this->chart_colors['background']['pattern'] = strtolower($pattern);
							$this->chart_colors['background']['angle'] = $angle;
							$this->chart_colors['background']['color2'] = str_replace('#', '', $color2);
							$this->chart_colors['background']['width'] = $var1;
							$this->chart_colors['background']['width2'] = $var2;
						} else
						$this->chart_colors['background']['pattern'] = strtolower($pattern);
					} else
						$this->chart_colors['background']['pattern'] = 'solid';
					break;
				case 'chart':
					$this->chart_colors['chart']['color'] = str_replace('#', '', $color);
					if(in_array(strtolower($pattern), $this->FILL_PATTERNS)) {
						if(strtolower($pattern) == 'gradient') {
							$this->chart_colors['chart']['pattern'] = strtolower($pattern);
							$this->chart_colors['chart']['angle'] = $angle;
							$this->chart_colors['chart']['color2'] = str_replace('#', '', $color2);
							$this->chart_colors['chart']['offset'] = $var1;
							$this->chart_colors['chart']['offset2'] = $var2;
						} elseif(strtolower($pattern) == 'stripes') {
							$this->chart_colors['chart']['pattern'] = strtolower($pattern);
							$this->chart_colors['chart']['angle'] = $angle;
							$this->chart_colors['chart']['color2'] = str_replace('#', '', $color2);
							$this->chart_colors['chart']['width'] = $var1;
							$this->chart_colors['chart']['width2'] = $var2;
						} else
							$this->chart_colors['chart']['pattern'] = strtolower($pattern);
					} else
						$this->chart_colors['chart']['pattern'] = 'solid';
					break;
			}
		}
	}

	class GoogleGraph {
		/*
			Description: Main object
			Useage: $graph = new GoogleGraph();
			Methods:
				'GoogleGraph'		=	Inititalizes object
				'printGraph'			=	Output graph
			Important Variables:
				'Graph'					=	Graph object
				'Data'					=	Data object
		*/
		var $BASE_ADDRESS = "http://chart.apis.google.com/chart?";

		var $Graph = null;
		var $Data = null;
		var $url = null;

		function GoogleGraph() {
			/*
				Description: Initializes object
				Important Variables:
					'Graph'				=	Create Graph object
					'Data'				=	Create Data object
			*/
			$this->Graph = new Graph();
			$this->Data = new Data();
		}

		function printGraph() {
			/*
				Description: Output graph
				Useage: $graph->Graph->printGraph();
			*/
			$url = $this->BASE_ADDRESS;

			$url .= 'cht=';
			switch($this->Graph->type) {
				case 'line':
					$url .= 'l';
					break;
				case 'bar':
					$url .= 'b';
					break;
				case 'pie':
					$url .= 'p';
					break;
				case 'venn':
					$url .= 'v';
					break;
				case 'scatter':
					$url .= 's';
					break;
			}
			switch($this->Graph->subtype) {
				case 'chart':
					$url .= 'c&';
					break;
				case 'axis':
					$url .= 'xy&';
					break;
				case 'horizontal_stacked':
					$url .= 'hs&';
					break;
				case 'vertical_stacked':
					$url .= 'vs&';
					break;
				case 'horizontal_grouped':
					$url .= 'hg&';
					break;
				case 'vertical_grouped':
					$url .= 'vg&';
					break;
				case '2d':
					$url .= '&';
					break;
				case '3d':
					$url .= '3&';
					break;
				case 'venn':
					$url .= '&';
					break;
				case 'scatter':
					$url .= '&';
					break;
			}
			if(!empty($this->Graph->title))
				$url .= 'chtt='.$this->Graph->title.'&';
			if(!empty($this->Graph->title_color) AND !empty($this->Graph->title_size))
				$url .= 'chts='.$this->Graph->title_color.','.$this->Graph->title_size.'&';
			else if(!empty($this->Graph->title))
				$url .= 'chts='.$this->Graph->title.'&';
			if(!empty($this->Graph->legend))
				$url .= 'chdl='.implode('|', $this->Graph->legend).'&';
			if(!empty($this->Graph->line_colors))
				$url .= 'chco='.implode(',', $this->Graph->line_colors).'&';
			if(!empty($this->Graph->bar_size))
				$url .= 'chbh='.intval($this->Graph->bar_size);
			if(!empty($this->Graph->chart_colors))
			{
				#$url .= 'chf=bg,s,00000000'; #transparent

				$url .= 'chf=';
				foreach($this->Graph->chart_colors as $key => $value) {
					switch($key) {
						case 'background':
							$url .= 'bg,';
							break;
						case 'chart':
							$url .= 'c,';
							break;
					}
					switch($value['pattern']) {
						case 'solid':
							$url .= 's,'.$value['color'].'|';
							break;
						case 'gradient':
							$url .= 'lg,'.$value['angle'].','.$value['color'].','.$value['offset'].','.$value['color2'].','.$value['offset2'].'|';
							break;
						case 'stripes':
							$url .= 'ls,'.$value['angle'].','.$value['color'].','.$value['width'].','.$value['color2'].','.$value['width2'].'|';
							break;
					}
				}
				$url = substr($url, 0, -1);

				$url .= '&';
			}
			if(!empty($this->Graph->chart_size)) {
				$url .= 'chs='.$this->Graph->chart_size.'&';
			}
			if(!empty($this->Data->data)) {
				$url .= 'chd=t:';
				foreach($this->Data->data as $data) {
					foreach($data as $datum) {
						$url .= number_format(intval($datum), 1, '.', '').',';
					}
					$url = substr($url, 0, -1);
					$url.= '|';
				}
				$url = substr($url, 0, -1);
				$url .= '&';
			}
			if(!empty($this->Graph->axis)) {
				$url .= 'chxt='.$this->Graph->axis.'&';
			}
			if(!empty($this->Graph->axis_labels)) {
				$url .= 'chxl=';
				$labelCount = 0;
				foreach($this->Graph->axis_labels as $value) {
					if(!empty($value)) {
						$url .= $labelCount.':|';
						foreach($value as $label) {
							$url .= $label.'|';
						}
						$labelCount++;
					} else
						$labelCount++;
				}
				$url .= '&';
			}
			if(!empty($this->Graph->label_positions)) {
				$url .= 'chxp=';
				foreach($this->Graph->label_positions as $position) {
					$url .= implode(',', $position).'|';
				}
				$url = substr($url, 0, -1);
				$url .= '&';
			}
			if(!empty($this->Graph->axis_ranges)) {
				$url .= 'chxr='.$this->Graph->axis_ranges.'&';     #$url .= 'chxp='.implode(',', $this->Graph->axis_ranges).'&';
			}
			if(!empty($this->Graph->axis_styles)) {
				$url .= 'chxs=';
				foreach($this->Graph->axis_styles as $axis_style) {
					$url .= str_replace('#', '', implode(',', $axis_style).'|');
				}
				$url .= substr($url, 0, -1);
				$url .= '&';
			}
			if(!empty($this->Graph->line_styles)) {
				$url .= 'chls=';
				foreach($this->Graph->line_styles as $line_style) {
					$url .= (implode(',', $line_style)).'|';
				}
				$url .= substr($url, 0, -1);
				$url .= '&';
			}
			if(!empty($this->Graph->grid_lines)) {
				$url .= 'chg='.$this->Graph->grid_lines.'&';
			}
			if(!empty($this->Graph->markers)) {
				$url .= 'chm=';
				foreach($this->Graph->markers as $marker) {
					$url .= (implode(',', str_replace('#', '', $marker))).'|';
				}
				$url = substr($url, 0, -1);
				$url .= '&';
			}

			$url = substr($url, 0, -1);
			$this->url = $url;

			$out_img= '<img src="'.$url.'"/>' ;
			return $out_img;

			#echo '<img src="'.$url.'"/>';
		}

		/* For Debug Only */
		function debug() {
			/*
				Description: Debug output
				Useage: $graph->Graph->debug();
			*/
			echo '<hr>';
			echo '-- URL --<br>';
			echo $this->url.'<br>';
			echo '-- Graph --<br>';
			if(!empty($this->Graph->type)) echo 'Type: '.$this->Graph->type.'<br>';
			if(!empty($this->Graph->subtype)) echo 'Subtype: '.$this->Graph->subtype.'<br>';
			if(!empty($this->Graph->title)) echo 'Title: '.$this->Graph->title.'<br>';
			if(!empty($this->Graph->title_color)) echo 'Title Color: '.$this->Graph->title_color.'<br>';
			if(!empty($this->Graph->title_size)) echo 'Title Size: '.$this->Graph->title_size.'<br>';
			if(!empty($this->Graph->legend)) echo 'Legend: '.print_r($this->Graph->legend).'<br>';
			if(!empty($this->Graph->line_colors)) echo 'Line Colors: '.print_r($this->Graph->line_colors).'<br>';
			if(!empty($this->Graph->bar_size)) echo 'Bar Size: '.$this->Graph->bar_size.'<br>';
			if(!empty($this->Graph->chart_colors)) echo 'Chart Colors: '.print_r($this->Graph->chart_colors).'<br>';
			if(!empty($this->Graph->chart_size)) echo 'Chart Size: '.$this->Graph->chart_size.'<br>';
			if(!empty($this->Graph->axis)) echo 'Axis: '.$this->Graph->axis.'<br>';
			if(!empty($this->Graph->axis_labels)) echo 'Axis Labels: '.print_r($this->Graph->axis_labels).'<br>';
			if(!empty($this->Graph->label_positions)) echo 'Label Positions: '.print_r($this->Graph->label_positions).'<br>';
			if(!empty($this->Graph->axis_ranges)) echo 'Axis Ranges: '.print_r($this->Graph->axis_ranges).'<br>';
			if(!empty($this->Graph->axis_styles)) echo 'Axis Styles: '.print_r($this->Graph->axis_styles).'<br>';
			if(!empty($this->Graph->line_styles)) echo 'Line Styles: '.print_r($this->Graph->line_styles).'<br>';
			if(!empty($this->Graph->grid_lines)) echo 'Grid Lines: '.$this->Graph->grid_lines.'<br>';
			if(!empty($this->Graph->markers)) echo 'Markers: '.print_r($this->Graph->markers).'<br>';
			echo '-- Data --<br>';
			if(!empty($this->Data->data)) echo 'Data: '.print_r($this->Data->data).'<br>';
		}
	}
?>