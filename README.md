<h1>The Template Class</h1>

<hr>

<h2>Brief: </h2>
<p>This is the templating class I have been working on and had working practise with in a few recent project - it's no where near perfect but doing the job so far.</p>

<p>The templating is based on [ ] brackets - although I am looking to move them too something less generic like {% %}</p>
<p>Setting up tags to replace just set an array for example: $tags and call each key the tag you want to replace -> <code>$tags['name'] = 'Ashley'</code></p>
<hr>

<h3>Example usage: </h3>
<p><strong>Standard Tags</strong></p>
<code>
  [BANANA]
</code>
<p><strong>Conditional Tags</strong></p>
<p><i>conditional statements are reliant on whether the variable is set to TRUE.</i></p>
<code>
  [IF SHOW_BANANA]
    <p>I am only shown if show_banana is true</p>
  [/ENDIF SHOW_BANANA]
</code>
<p><strong>Foreach Tags</strong></p>
<p><i>Foreach loops are based on associative arrays within an array ( two dimensional ).</i><br>
Example: <code>array ( array ( 'name' => 'Ashley Banks' ), array ( 'name' => 'Banana man' ) )</code></p>
<code>
  [FOREACH banana as b]
    <p>My name is: [b.name]</p>
  [/banana FOREACH]
</code>

