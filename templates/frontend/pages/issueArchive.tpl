{**
 * templates/frontend/pages/issueArchive.tpl
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @brief Display a list of recent issues.
 *
 * @uses $issues Array Collection of issues to display
 * @uses $prevPage int The previous page number
 * @uses $nextPage int The next page number
 * @uses $showingStart int The number of the first item on this page
 * @uses $showingEnd int The number of the last item on this page
 * @uses $total int Count of all published monographs
 *}
{capture assign="pageTitle"}
	{if $prevPage}
		{translate key="archive.archivesPageNumber" pageNumber=$prevPage+1}
	{else}
		{translate key="archive.archives"}
	{/if}
{/capture}
{include file="frontend/components/header.tpl" pageTitleTranslated=$pageTitle}

<div class="page page_issue_archive">
	{include file="frontend/components/breadcrumbs.tpl" currentTitle=$pageTitle}

	{* No issues have been published *}
	{if empty($issues)}
		<p>{translate key="current.noCurrentIssueDesc"}</p>

	{* List issues *}
	{else}

	  <ul class="issues_archive">
	  	{foreach from=$issues item="issue"}
	  		{if $issue->getYear() != $lastYear}
	  			<li><h3>{$issue->getYear()|escape}</h3></li>
	  			{assign var=lastYear value=$issue->getYear()}
	  		{/if}
	  		<li>
	  			{include file="frontend/objects/issue_summary.tpl"}
	  		</li>
	  	{/foreach}
	  </ul>

		{* Pagination *}
		{if $prevPage > 1}
			{url|assign:"prevUrl" router=$smarty.const.ROUTE_PAGE page="issue" op="archive" path=$prevPage}
		{elseif $prevPage === 1}
			{url|assign:"prevUrl" router=$smarty.const.ROUTE_PAGE page="issue" op="archive"}
		{/if}
		{if $nextPage}
			{url|assign:"nextUrl" router=$smarty.const.ROUTE_PAGE page="issue" op="archive" path=$nextPage}
		{/if}
		{include
			file="frontend/components/pagination.tpl"
			prevUrl=$prevUrl
			nextUrl=$nextUrl
			showingStart=$showingStart
			showingEnd=$showingEnd
			total=$total
		}
	{/if}
</div>

{include file="frontend/components/footer.tpl"}
