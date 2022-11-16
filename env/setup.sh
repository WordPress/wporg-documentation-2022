# #!/bin/bash

root=$( dirname $( wp config path ) )

wp theme activate wporg-support

wp rewrite structure '/%postname%/'
wp rewrite flush --hard

wp option update blogname "Documentation"
wp option update blogdescription "We’ve got a variety of resources to help you get the most out of WordPress."

wp import "$root/env/data/docs-pages.xml" --authors=skip
wp import "$root/env/data/docs-articles.xml" --authors=skip
wp import "$root/env/data/docs-versions.xml" --authors=skip
wp import "$root/env/data/docs-menus.xml" --authors=skip

wp option update show_on_front 'page'
wp option update page_on_front 8

wp option update sidebars_widgets "{\"front-page-blocks\":[\"helphub_front_page_block-2\",\"helphub_front_page_block-3\",\"helphub_front_page_block-4\",\"helphub_front_page_block-5\",\"helphub_front_page_block-6\",\"helphub_front_page_block-7\",\"helphub_front_page_block-8\",\"helphub_front_page_block-9\",\"helphub_front_page_block-10\"],\"helphub-sidebar\":[\"nav_menu-2\"],\"array_version\":3}" --format=json
wp option update widget_helphub_front_page_block "{\"2\":{\"icon\":\"dashicons-wordpress\",\"title\":\"Getting Started\",\"description\":\"Learn about WordPress, both as a free software, and a community.\",\"categoryid\":\"591444\",\"menu\":\"614856\"},\"3\":{\"icon\":\"dashicons-download\",\"title\":\"Installing WordPress\",\"description\":\"It\\u2019s easy to install WordPress. Dive in to learn more!\",\"categoryid\":\"591446\",\"menu\":\"614858\"},\"4\":{\"icon\":\"dashicons-welcome-widgets-menus\",\"title\":\"Basic Usage\",\"description\":\"Write and edit posts and pages with your text, images and other media.\",\"categoryid\":\"591441\",\"menu\":\"614875\"},\"5\":{\"icon\":\"dashicons-admin-generic\",\"title\":\"Basic Administration\",\"description\":\"Learn about your website's settings, permalinks, and other useful features.\",\"categoryid\":\"591440\",\"menu\":\"614876\"},\"6\":{\"icon\":\"dashicons-admin-plugins\",\"title\":\"Customizing\",\"description\":\"Find the right themes, plugins, widgets to make your site match your needs.\",\"categoryid\":\"591442\",\"menu\":\"614877\"},\"7\":{\"icon\":\"dashicons-admin-tools\",\"title\":\"Maintenance\",\"description\":\"Backup, PHP versions, streamlining or even automating your regular tasks.\",\"categoryid\":\"591447\",\"menu\":\"614878\"},\"8\":{\"icon\":\"dashicons-lock\",\"title\":\"Security\",\"description\":\"WordPress is pretty secure out-of-the box. But don\\u2019t open yourself up for vulnerabilities.\",\"categoryid\":\"591450\",\"menu\":\"614879\"},\"9\":{\"icon\":\"dashicons-performance\",\"title\":\"Advanced Topics\",\"description\":\"WordPress is very flexible and versatile. Here are some examples of what you can do, just to get your imagination started.\",\"categoryid\":\"591439\",\"menu\":\"614880\"},\"10\":{\"icon\":\"dashicons-sos\",\"title\":\"Troubleshooting\",\"description\":\"Is anything wrong? Did you get hacked? First: continue to breathe. Next, have a look at these resources\",\"categoryid\":\"591452\",\"menu\":\"614881\"},\"_multiwidget\":1}" --format=json
wp option update widget_nav_menu "{\"2\":{\"title\":\"Categories\",\"nav_menu\":614891},\"_multiwidget\":1}" --format=json
