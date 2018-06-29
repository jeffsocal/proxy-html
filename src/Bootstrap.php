<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML;

class Bootstrap extends Display
{

    private $sizes = array();

    private $types = array();

    //
    public function __construct($main = NULL, $body = NULL)
    {
        parent::__construct($main, $body);
        
        $this->sizes = array(
            'large',
            'medium',
            'small',
            'tiny'
        );
        
        $this->types = array(
            'default',
            'primary',
            'success',
            'info',
            'warning',
            'danger',
            'link'
        );
    }

    //
    private function translateSize($size)
    {
        $s = strtolower($size);
        $this_arr = preg_grep('/^' . $s . '/', $this->sizes);
        
        if (sizeof($this_arr) > 0) {
            foreach ($this_arr as $type) {
                return $type;
            }
        }
        return 'medium';
    }

    //
    private function translateType($type)
    {
        $s = strtolower($type);
        $this_arr = preg_grep('/^' . $s . '/', $this->types);
        
        if (sizeof($this_arr) > 0) {
            foreach ($this_arr as $type) {
                return $type;
            }
        }
        
        return 'default';
    }

    //
    public function row($layout = '{"md":[6,6]}', $array)
    {
        
        
        if (! strstr($layout, "{")) {
            $layout = explode("-", $layout);
            $layout = array(
                "md" => $layout
            );
            $layout = json_encode($layout);
        }
        
        $layout = json_decode($layout, JSON_OBJECT_AS_ARRAY);
        
        $htm = '<div class="row">';
        
        foreach ($array as $n => $content) {
            $htm .= '<div class="';
            
            foreach ($layout as $size => $facet) {
                $htm .= 'col-' . $size . '-' . $facet[$n] . ' ';
            }
            
            $htm .= '">';
            $htm .= $content;
            $htm .= '</div>';
        }
        
        $htm .= '</div>';
        
        return $htm;
    }

    //
    public function rows($array)
    {
        $htm = '';
        foreach ($array as $row) {
            
            $cols = sizeof($row);
            if ($cols = 5)
                $cols ++;
            $colw = floor(12 / $cols);
            $layout = implode("-", array_fill(0, $colw, $cols));
            
            $htm .= $this->row($layout, $row);
        }
        return $htm;
    }

    //
    public function link($str, $url)
    {
        $htm = '<a class="link" 
                href="' . $url . '">
                ' . $str . '</a>';
        return $htm;
    }

    //
    public function link_ajax($str, $id = "#")
    {
        $htm = '<a class="link"
				id="' . $id . '">
				' . $str . '</a>';
        
        return $htm;
    }

    //
    public function mailto_link($address, $subject = '')
    {
        $htm = '<a href="mailto:' . $address . '?subject=' . urlencode($subject) . '">Email Us</a>';
        return $htm;
    }

    public function mailto_button($address, $subject = '')
    {
        $url = 'mailto:' . $address . '?subject=' . urlencode($subject);
        return $this->button("Email Us", $url);
    }

    //
    public function container($text)
    {
        $htm = '<div class="container">' . $text . '</div>';
        return $htm;
    }

    //
    public function pageheader($str = '', $ptsize = 3)
    {
        $htm = '<div class="page-header">
				' . $this->header($str, $ptsize) . '
				</div>';
        return $htm;
    }

    //
    public function header($str = '', $ptsize = 5)
    {
        $htm = '<h' . $ptsize . '>' . $str . '</h' . $ptsize . '>';
        return $htm;
    }

    //
    public function button($str, $url = "#", $type = "d", $size = "m")
    {
        $size = $this->translateSize($size);
        
        $b_size = '';
        if ($size == 'large')
            $b_size = 'btn-lg';
        if ($size == 'small')
            $b_size = 'btn-sm';
        if ($size == 'tiny')
            $b_size = 'btn-xs';
        
        $htm = '<button type="button"
				class="btn
				' . $b_size . '
				btn-' . $this->translateType($type) . '"
				onclick="location.href=\'' . $url . '\';">
				' . $str . '</button>';
        
        return $htm;
    }

    //
    public function button_ajax($str, $id = "#", $type = "d", $size = "m")
    {
        $size = $this->translateSize($size);
        
        $b_size = '';
        if ($size == 'large')
            $b_size = 'btn-lg';
        if ($size == 'small')
            $b_size = 'btn-sm';
        if ($size == 'tiny')
            $b_size = 'btn-xs';
        
        $htm = '<button type="button"
				class="btn
				' . $b_size . '
				btn-' . $this->translateType($type) . '"
				id="' . $id . '">
				' . $str . '</button>';
        
        return $htm;
    }

    //
    public function table($table_array, $header = true, $is_striped = false)
    {
        $htm = new Tables($is_striped);
        return $htm->table_ashtml($table_array, $header);
    }

    //
    public function image_circle($src, $width = 150, $height = 150)
    {
        return $this->image_htm($src, $width, $height, 'circle');
    }

    public function image_thumbnail($src, $width = 150, $height = 150)
    {
        return $this->image_htm($src, $width, $height, 'thumbnail');
    }

    public function image_responsive($src, $width = 150, $height = 150)
    {
        return $this->image_htm($src, $width, $height, 'responsive');
    }

    public function image_fluid($src, $width = 150, $height = 150)
    {
        return $this->image_responsive($src, $width, $height);
    }

    public function image($src, $width = 150, $height = 150)
    {
        return $this->image_htm($src, $width, $height);
    }

    //
    private function image_htm($src, $width = 150, $height = 150, $type = '')
    {
        $htm = '<img src="' . $src . '"
				width="' . $width . '" height="' . $height . '">';
        
        if ($type == 'circle') {
            $htm = '<img class="rounded-circle" src="' . $src . '"
					alt="" width="' . $width . '" height="' . $height . '">';
        }
        if ($type == 'thumbnail') {
            $htm = '<img
					class="img-thumbnail"
					data-src="holder.js/' . $width . 'x' . $height . '"
					alt=""
					src="' . $src . '"
					style="width: ' . $width . 'px; height: ' . $height . 'px;"
					data-holder-rendered="true">';
        }
        if ($type == 'responsive') {
            $htm = '<img
					class="image img-fluid center-block"
					data-src="holder.js/' . $width . 'x' . $height . '/auto"
					alt=""
					src="' . $src . '"
					data-holder-rendered="true">';
        }
        return $htm;
    }

    //
    public function label($str, $type = "d", $ptsize = 3, $id = false)
    {
        $htm = '<span class="badge
				badge-' . $this->translateType($type);
        if (! is_false($id))
            $htm .= ' id="' . $id . '"';
        
        $htm .= '">' . $str . '
				</span>';
        return $htm;
    }

    //
    public function text($str, $ptsize = 0.8)
    {
        $htm = '<p style="font-size: ' . $ptsize . 'em;">' . $str . '</p>';
        return $htm;
    }

    //
    public function notify($str)
    {
        return '<span class="badge">' . $str . '</span>';
    }

    // LISTS OF SOME SORT
    //
    private function listArray($array, $listas = '')
    {
        $htm = '';
        foreach ($array as $name => $link) {
            $value = false;
            $active = false;
            
            if (preg_match("/\:\d+$/", $name)) {
                $value = preg_replace("/.*\:/", "", $name);
                $name = preg_replace("/\:.*/", "", $name);
            }
            
            if (preg_match("/^\*/", $name))
                $active = true;
            
            $name = preg_replace("/\*/", "", $name);
            
            if ($listas != 'class="list-group-item"' and $link == '') {
                $htm .= '<li ' . $listas . ' ';
                if (! is_false($active))
                    $htm .= 'class="active"';
                $htm .= '>';
                
                $listas = '';
            }
            
            if ($link != '')
                $htm .= '<a href="' . $link . '" ' . $listas . '>';
            $htm .= $name;
            if (! is_false($link))
                $htm .= '</a>';
            
            if (! is_false($value))
                $htm .= '<span class="badge">' . $value . '</span>';
            
            if ($listas != 'class="list-group-item"' and $link == '')
                $htm .= '</li>';
        }
        return $htm;
    }

    //
    public function badges($array)
    {
        $htm = '<ul class="nav nav-pills" role="tablist">';
        foreach ($array as $name => $link) {
            $value = false;
            $active = false;
            
            if (preg_match("/\:\d+\.*\d*$/", $name)) {
                $value = preg_replace("/.*\:/", "", $name);
                $name = preg_replace("/\:.*/", "", $name);
            }
            
            if (preg_match("/^\*/", $name))
                $active = true;
            
            $name = preg_replace("/\*/", "", $name);
            
            $htm .= '<li role="presentation"';
            if ($active == true)
                $htm .= ' class="active"';
            $htm .= '>';
            $htm .= '<a href="' . $link . '">';
            $htm .= $name;
            $htm .= '<span class="badge">' . $value . '</span></a></li>';
        }
        $htm .= '</ul>';
        
        return $htm;
    }

    //
    public function listgroup($array, $linked = false)
    {
        if (is_false($linked))
            $htm = '<ul class="list-group">';
        else
            $htm = '<div class="list-group">';
        
        foreach ($array as $name => $link) {
            $value = false;
            $active = false;
            
            if (preg_match("/\:\d+$/", $name)) {
                $value = preg_replace("/.*\:/", "", $name);
                $name = preg_replace("/\:.*/", "", $name);
            }
            
            if (preg_match("/^\*/", $name))
                $active = true;
            
            $name = preg_replace("/\*/", "", $name);
            
            if (is_false($linked)) {
                $htm .= '<li class="list-group-item">';
                $htm .= $name;
                $htm .= '</li>';
            } else {
                
                $htm .= '<a href="' . $link . '" class="list-group-item';
                if (! is_false($active))
                    $htm .= ' active';
                $htm .= '">';
                $htm .= $name;
                $htm .= '</a>';
                $htm .= '</li>';
            }
        }
        
        if (is_false($linked))
            $htm .= '</ul>';
        else
            $htm .= '</div>';
        
        return $htm;
    }

    //
    public function listgrouptext($array)
    {
        $htm = '<div class="list-group">';
        
        foreach ($array as $name => $contents) {
            
            $link = $contents['link'];
            $head = $contents['head'];
            $text = $contents['text'];
            
            $active = false;
            if (preg_match("/^\*/", $name))
                $active = true;
            
            $htm .= '<a href="' . $link . '" class="list-group-item';
            if (! is_false($active))
                $htm .= ' active';
            $htm .= '">';
            $htm .= '<h4 class="list-group-item-heading">' . $head . '</h4>';
            $htm .= '<p class="list-group-item-text">' . $text . '</p>';
            $htm .= '</a>';
            $htm .= '</li>';
        }
        
        $htm .= '</div>';
        
        return $htm;
    }

    //
    public function dropdown($array, $name = "Dropdown")
    {
        $htm = '<div class="dropdown theme-dropdown clearfix">';
        
        $htm .= '<a id="dropdownMenu1" href="#" ';
        $htm .= 'class="sr-only dropdown-toggle" ';
        $htm .= 'data-toggle="dropdown" role="button" ';
        $htm .= 'aria-haspopup="true" aria-expanded="false">';
        $htm .= $name . ' <span class="caret"></span></a>';
        $htm .= '<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">';
        foreach ($array as $name => $link) {
            $value = false;
            $active = false;
            
            if (preg_match("/^\*/", $name))
                $active = true;
            
            $name = preg_replace("/\*/", "", $name);
            
            $htm .= '<li ';
            if ($active == true)
                $htm .= ' class="active"';
            $htm .= '>';
            $htm .= '<a href="' . $link . '">';
            $htm .= $name;
            $htm .= '</a></li>';
        }
        $htm .= '</ul></div>';
        
        return $htm;
    }

    //
    public function alert($catch, $text = '', $type = 'i')
    {
        $a_type = 'info';
        $type = $this->translateType($type);
        if (strstr("info,success,warning,danger", $type))
            $a_type = $type;
        
        $htm = '<div class="alert alert-' . $a_type . '"
				role="alert">
				<strong>' . $catch . '</strong>' . $this->sp(1) . '
				' . $text . '
				</div>';
        return $htm;
    }

    //
    public function progressbar($value = 0, $type = 's')
    {
        $type = $this->translateType($type);
        
        $htm = '<div class="progress">
				<div class="progress-bar progress-bar-' . $type . '"
				role="progressbar"
				aria-valuenow="' . $value . '"
				aria-valuemin="0"
				aria-valuemax="100"
				style="width: ' . $value . '%;">
				<span class="sr-only">' . $value . '% Complete</span>
				</div></div>';
        
        return $htm;
    }

    //
    public function panel($title, $text, $type = 'd')
    {
        $htm = $this->card($title, $text);
        // $type = $this->translateType($type);
        
        // $htm = '<div class="panel panel-' . $type . '">
        // <div class="panel-heading">
        // <h3 class="panel-title">' . $title . '</h3>
        // </div>
        // <div class="panel-body">
        // ' . $text . '
        // </div></div>';
        
        return $htm;
    }

    //
    public function well($text)
    {
        $htm = '<div class="well">
				<p>' . $text . '</p>
				</div>';
        
        return $htm;
    }

    //
    public function carousel()
    {
        // $htm = '<div id="carousel-example-generic" class="carousel slide" data-ride
        // <ol class="carousel-indicators">
        // <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
        // <li data-target="#carousel-example-generic" data-slide-to="1" class=""></li>
        // <li data-target="#carousel-example-generic" data-slide-to="2" class=""></li>
        // </ol>
        // <div class="carousel-inner" role="listbox">
        // <div class="item active">
        // <img data-src="holder.js/1140x500/auto/#777:#555/text:First slide" alt="First slide [1140x500]" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMTE0MCIgaGVpZ2h0PSI1MDAiIHZpZXdCb3g9IjAgMCAxMTQwIDUwMCIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+PCEtLQpTb3VyY2UgVVJMOiBob2xkZXIuanMvMTE0MHg1MDAvYXV0by8jNzc3OiM1NTUvdGV4dDpGaXJzdCBzbGlkZQpDcmVhdGVkIHdpdGggSG9sZGVyLmpzIDIuNi4wLgpMZWFybiBtb3JlIGF0IGh0dHA6Ly9ob2xkZXJqcy5jb20KKGMpIDIwMTItMjAxNSBJdmFuIE1hbG9waW5za3kgLSBodHRwOi8vaW1za3kuY28KLS0+PGRlZnM+PHN0eWxlIHR5cGU9InRleHQvY3NzIj48IVtDREFUQVsjaG9sZGVyXzE1YWNkNTkzYjMyIHRleHQgeyBmaWxsOiM1NTU7Zm9udC13ZWlnaHQ6Ym9sZDtmb250LWZhbWlseTpBcmlhbCwgSGVsdmV0aWNhLCBPcGVuIFNhbnMsIHNhbnMtc2VyaWYsIG1vbm9zcGFjZTtmb250LXNpemU6NTdwdCB9IF1dPjwvc3R5bGU+PC9kZWZzPjxnIGlkPSJob2xkZXJfMTVhY2Q1OTNiMzIiPjxyZWN0IHdpZHRoPSIxMTQwIiBoZWlnaHQ9IjUwMCIgZmlsbD0iIzc3NyIvPjxnPjx0ZXh0IHg9IjM5MC41MDc4MTI1IiB5PSIyNzUuNSI+Rmlyc3Qgc2xpZGU8L3RleHQ+PC9nPjwvZz48L3N2Zz4=" data-holder-rendered="true">
        // </div>
        // <div class="item">
        // <img data-src="holder.js/1140x500/auto/#666:#444/text:Second slide" alt="Second slide [1140x500]" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMTE0MCIgaGVpZ2h0PSI1MDAiIHZpZXdCb3g9IjAgMCAxMTQwIDUwMCIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+PCEtLQpTb3VyY2UgVVJMOiBob2xkZXIuanMvMTE0MHg1MDAvYXV0by8jNjY2OiM0NDQvdGV4dDpTZWNvbmQgc2xpZGUKQ3JlYXRlZCB3aXRoIEhvbGRlci5qcyAyLjYuMC4KTGVhcm4gbW9yZSBhdCBodHRwOi8vaG9sZGVyanMuY29tCihjKSAyMDEyLTIwMTUgSXZhbiBNYWxvcGluc2t5IC0gaHR0cDovL2ltc2t5LmNvCi0tPjxkZWZzPjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+PCFbQ0RBVEFbI2hvbGRlcl8xNWFjZDU4ZTBlMSB0ZXh0IHsgZmlsbDojNDQ0O2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1mYW1pbHk6QXJpYWwsIEhlbHZldGljYSwgT3BlbiBTYW5zLCBzYW5zLXNlcmlmLCBtb25vc3BhY2U7Zm9udC1zaXplOjU3cHQgfSBdXT48L3N0eWxlPjwvZGVmcz48ZyBpZD0iaG9sZGVyXzE1YWNkNThlMGUxIj48cmVjdCB3aWR0aD0iMTE0MCIgaGVpZ2h0PSI1MDAiIGZpbGw9IiM2NjYiLz48Zz48dGV4dCB4PSIzMzUuNjAxNTYyNSIgeT0iMjc1LjUiPlNlY29uZCBzbGlkZTwvdGV4dD48L2c+PC9nPjwvc3ZnPg==" data-holder-rendered="true">
        // </div>
        // <div class="item">
        // <img data-src="holder.js/1140x500/auto/#555:#333/text:Third slide" alt="Third slide [1140x500]" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMTE0MCIgaGVpZ2h0PSI1MDAiIHZpZXdCb3g9IjAgMCAxMTQwIDUwMCIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+PCEtLQpTb3VyY2UgVVJMOiBob2xkZXIuanMvMTE0MHg1MDAvYXV0by8jNTU1OiMzMzMvdGV4dDpUaGlyZCBzbGlkZQpDcmVhdGVkIHdpdGggSG9sZGVyLmpzIDIuNi4wLgpMZWFybiBtb3JlIGF0IGh0dHA6Ly9ob2xkZXJqcy5jb20KKGMpIDIwMTItMjAxNSBJdmFuIE1hbG9waW5za3kgLSBodHRwOi8vaW1za3kuY28KLS0+PGRlZnM+PHN0eWxlIHR5cGU9InRleHQvY3NzIj48IVtDREFUQVsjaG9sZGVyXzE1YWNkNTkyYjcyIHRleHQgeyBmaWxsOiMzMzM7Zm9udC13ZWlnaHQ6Ym9sZDtmb250LWZhbWlseTpBcmlhbCwgSGVsdmV0aWNhLCBPcGVuIFNhbnMsIHNhbnMtc2VyaWYsIG1vbm9zcGFjZTtmb250LXNpemU6NTdwdCB9IF1dPjwvc3R5bGU+PC9kZWZzPjxnIGlkPSJob2xkZXJfMTVhY2Q1OTJiNzIiPjxyZWN0IHdpZHRoPSIxMTQwIiBoZWlnaHQ9IjUwMCIgZmlsbD0iIzU1NSIvPjxnPjx0ZXh0IHg9IjM3Ny44NjcxODc1IiB5PSIyNzUuNSI+VGhpcmQgc2xpZGU8L3RleHQ+PC9nPjwvZz48L3N2Zz4=" data-holder-rendered="true">
        // </div>
        // </div>
        // <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
        // <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        // <span class="sr-only">Previous</span>
        // </a>
        // <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
        // <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        // <span class="sr-only">Next</span>
        // </a>
        // </div>';
        // return $htm;
    }

    //
    public function jumbotron($title, $text)
    {
        $htm = '<div class="jumbotron">
        		' . $this->header($title, 1) . '
        		<p>' . $text . '</p>
      			</div>';
        return $htm;
    }

    /*
     * CARDS Bootstrap 4.0
     */
    public function card($title, $text, $link = '#', $image = FALSE, $date = FALSE)
    {
        $htm = '';
        $htm .= ' <div class="card">';
        $htm .= ' <div class="card-body">';
        $htm .= ' <h3 class="card-title"><a class="text-muted" href="' . $link . '">' . $title . '</a></h3>';
        
        if (! is_false($date))
            $htm .= ' <div class="mb-1 text-muted">' . $date . '</div>';
        
        $htm .= ' <p class="card-text">' . $text . '</p>';
        
        // if ($link != '#')
        // $htm .= ' <a href="' . $link . '">Continue reading</a>';
        $htm .= ' </div>';
        
        if (! is_false($image))
            $htm .= ' <img class="art-image" src="' . $image . '">';
        
        $htm .= ' </div>';
        
        return $htm;
    }
}
?>