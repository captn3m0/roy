#Roy

Roy is a todo application for Slack users.

##Features
1. Create an item from slack chat.
2. See all items on the web application.
3. Filter & sort items by mentions/hashtags or creation dates
4. Mark items as done.
5. See a calendar of all items, and when they were closed.
6. Receive daily emails about what all todo items were created or closed today.

##Stack
Roy uses the Slack-API for authentication, and Parse for storing the list-items. It uses Link for routing. Its written in PHP, because Parse only supports PHP on the server-side right now. Most of the app logic is written in JS, and it uses routie and tooltipster libraries on the client-side.

##Deploy
The application will be directly deployable to Heroku once it is completed using the Heroku Button.

##Setup on Slack
Create an outgoing webhook on slack to point to /item. Make the webhook listen on a keyword ("roy"), instead of a channel. Note that Roy won't receive messages from private groups. We can also try to make this an official slack integration (I've contacted slack on this).

##Configuration
Currently, only Parse credentials are required for an install. Some other configuration options will be made available on the team settings page.

##Hosting
You are free to self-host Roy, but note that Roy is built with multi-tenancy in mind. Therefore, a single Roy setup can work with multiple Slack teams.

##Security
Once you've setup Roy, you can use secure your Roy setup by configuring the token in the team settings page. You can also make your todo list public to the world with a switch flip.

##Screenshots
TODO

##Slack Commands
You can also communicate with Roy using the Slack chat-interface instead of opening the web-ui. The following options are available:

- *create* - This is the default option. Any message that roy receives creates a new item on your team's todo list. All hashtags and mentions are preserved and searchable. Note that you don't need to type `create`. Just type `roy @nemo is working on something`, and Roy will pick it up.
- *list [keyword]* - You can list all your items matching a required keyword. This keyword can either be a `#hashtag` or a `@mention`.
- *show [keyword]* - This is an alias to list.
- *link* - Roy will reply with a link to your team page on the Roy web app, where you can see all your todo items.

##Licence
Licened under [MIT Licence](http://rem.mit-license.org)