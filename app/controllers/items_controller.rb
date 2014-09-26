class ItemsController < ApplicationController

  # We'll instead use the auth token that slack provides
  skip_before_filter :verify_authenticity_token, :only => [:create]

  # This is creation of an item
  # from the slack webhook
  def create
    render_slack params[:text]
    return
    item = Item.create_from_webhook params
    if item.nil?
      message = "Please login to Roy first to create a team."  
    else
      message = I18n.t('item_create_response').sample
    end
    render_slack message
  end

  def index
    items = Item.all
    render json: items
  end
end
