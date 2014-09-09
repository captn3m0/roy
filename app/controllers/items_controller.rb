class ItemsController < ActionController::Base
  # This is creation of an item
  # from the slack webhook
  def create
    Item.create_from_webhook params
    render json: {text: "Saved"}
  end

  def index
    items = Item.all
    render json: items
  end
end
