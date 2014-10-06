class ItemsController < ApplicationController

  # We'll instead use the auth token that slack provides
  skip_before_filter :verify_authenticity_token, :only => [:create]

  # This is creation of an item
  # from the slack webhook
  def create
    # This is to make amon ignore its own sayings
    return if params[:user_id].eql?("USLACKBOT")

    # Try to create a new item
    item = Item.create_from_webhook params
    if item.nil?
      message = I18n.t(:no_such_team, :url=>"#{request.env['HTTP_HOST']}/auth/slack")
    else
      message = I18n.t('item_create_response').sample
    end
    render_slack message
  end  

  # The only way to update an item is to mark it as done
  def update
    # TODO - Check if user has permissions to do this
    item = Item.find params[:id]
    item.touch
    render plain: "The item was marked as done"
  end
end