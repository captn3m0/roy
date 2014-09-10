class ItemsController < ApplicationController

  # We'll instead use the auth token that slack provides
  skip_before_filter :verify_authenticity_token, :only => [:create]

  # This is creation of an item
  # from the slack webhook
  def create
    item = Item.create_from_webhook params
    message = I18n.t('item_create_response').sample
    render_slack message
  end

  def index
    items = Item.all
    render json: items
  end
end
