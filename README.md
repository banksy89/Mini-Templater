<h1>The Template Class</h1>

<h2>Background Story: </h2>
<p>So I decided I wanted to remove all PHP from any view I use. The only bit of PHP I ever have in a view is the occasional IF/ELSE Statement or a Foreach Loop.</p>
<p>I looked into Smarty, tried it but then thought - nah it's too much and funky stuff started happening when trying to include it in my Framework. So I decided I would just start writing my own one!</p>

<hr>

<h2>What this does: </h2>

<p>This template class relies on [ ] tags - I moved away from the 'Mustache' style because Mustache and Smarty both use these.</p>
<p>The main thing this does at the moment is allow a foreach loop tag to be in the view - by a provided array.</p>

<p>For ease: I have included at the bottom of the class just a simple Demo of how to use it. I'll keep this as it's own repo for now - and then update my Framework to include this template handler.</p>

<hr>

<p>This is just the beginning - in total I have probably spent over a couple of hours on it. So IT IS NOT PERFECT!</p>