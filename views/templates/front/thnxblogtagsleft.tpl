{if isset($thnxblogtags)}
<div class="thnxblogtags block tags_block">
	<h2 class="title_block">
		{if isset($thnxbt_title)}{$thnxbt_title}{/if}
	</h2>
	<div class="block_content">
		<ul class="clearfix">
			{foreach from=$thnxblogtags item=thnxblogtag}
				<li>
					<a href="{$thnxblogtag.link}" title="{$thnxblogtag.name}">
						{$thnxblogtag.name}
					</a>
							
				</li>
			{/foreach}
		</ul>
	</div>
</div>
{else}
	<p>{l s='No tags have been specified yet.' mod='thnxblogtags'}</p>
{/if}