{*** Domain Aliases ***}
<table id="alias_domain_table">
	<tr>
		<th colspan="5">{$PALANG.pOverview_alias_domain_title}</th>
	</tr>
	{if $tAliasDomains|@count>0 || $tTargetDomain|@count>1}
		{if $tAliasDomains|@count>0} {* -> HAT alias-domains *}
			{#tr_header#}
			<td>{$PALANG.pOverview_alias_address}</td>
			<td>{$PALANG.pOverview_alias_goto}</td>
			<td>{$PALANG.pOverview_alias_domain_modified}</td>
			<td>{$PALANG.pOverview_alias_domain_active}</td>
			<td>&nbsp;</td>
			</tr>
			{foreach from=$tAliasDomains item=item}
				{#tr_hilightoff#}
				<td>{if $item.alias_domain != $fDomain}<a href="{$smarty.config.url_list_virtual}?domain={$item.alias_domain|escape:"url"}&amp;limit={$current_limit|escape:"url"}">{/if}
					{if $search eq ""}
						{$item.alias_domain}
					{else}
						{$item.alias_domain|replace:$search:"<span class='searchresult'>$search</span>"}
					{/if}
					{if $item.alias_domain != $fDomain}</a>{/if}</td>
				<td>{if $item.target_domain != $fDomain}<a href="{$smarty.config.url_list_virtual}?domain={$item.target_domain|escape:"url"}&amp;limit={$current_limit|escape:"url"}">{/if}
					{if $search eq ""}
						{$item.target_domain}
					{else}
						{$item.target_domain|replace:$search:"<span class='searchresult'>$search</span>"}
					{/if}
					{if $item.target_domain != $fDomain}</a>{/if}</td>
				<td>{$item.modified}</td>
				<td><a href="{#url_edit_active#}?alias_domain=true&amp;domain={$item.alias_domain|escape:"url"}&amp;return={$smarty.config.url_list_virtual|escape:"url"}?domain={$fDomain|escape:"url"}&amp;limit={$current_limit|escape:"url"}">{if $item.active==1}{$PALANG.YES}{else}{$PALANG.NO}{/if}</a></td>
				<td><a href="{#url_delete#}?table=alias_domain&amp;delete={$item.alias_domain|escape:"url"}&amp;domain={$item.alias_domain|escape:"url"}" onclick="return confirm ('{$PALANG.confirm}{$PALANG.pOverview_get_alias_domains}: {$item.alias_domain}');">{$PALANG.del}</a></td>
				</tr>
			{/foreach}
		{/if}
	{/if}
</table>
{if $can_create_alias_domain}
	<br/>
	<p id="mailbox_table"><a href="{#url_create_alias_domain#}?target_domain={$fDomain|escape:"url"}">{$PALANG.pMenu_create_alias_domain}</a></p>
{/if}
