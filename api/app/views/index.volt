<html>
<head></head>
<body>
{% set musttab = false %}
{% set tab = false %}
{% for i, entry in entries %}
    {% if ((entry.relevance < 7) or i > 8) and not musttab %}
    {#{% if ((entry.relativeRelevance < 3 and entry.relevance < 2) or i > 8) and not musttab %}#}
        {% set musttab = true %}<hr/>
    {% endif %}

    {% if ((entry.relevance < 1.8) or i > 22) and not tab %}
    {#{% if ((entry.relativeRelevance < 1.3 and entry.relevance < 0.8) or i > 22) and not tab %}#}
        {% set tab = true %}<hr/>
    {% endif %}

    <a href="{{ entry.link }}">{{ entry.title }}</a> - {{ entry.getSourceEntity().title }}
    <span>[<?php echo number_format($entry->relevance, 2) ?>]</span>
    <span>[<?php echo number_format($entry->relativeRelevance, 2) ?>]</span>
    <br>

    {#<span style="color: #999">#}
        {#{{ date('d-m-Y h:i', entry.publishedAt) }} - [{{ entry.categories|join(', ') }}]#}
    {#</span>#}
    {#<br>#}

    {#<span style="color: #999">#}
        {#Tweets: <?php echo number_format($entry->relativeStats['twitter']['count'], 2) ?>;#}
        {#Likes: <?php echo number_format($entry->relativeStats['fb']['likes'], 2) ?>;#}
        {#Comments: <?php echo number_format($entry->relativeStats['fb']['comments'], 2) ?>;#}
        {#Shares: <?php echo number_format($entry->relativeStats['fb']['shares'], 2) ?>;#}
        {#Clicks: <?php echo number_format($entry->relativeStats['fb']['click'], 2) ?>;#}
    {#</span>#}
    {#<br>#}

    <br>
{% endfor %}
</body>
</html>