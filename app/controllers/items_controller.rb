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

  def index
    session[:team] ||= []
    raise "No team specified" if params[:team].nil?
    @team = Team.find_by(name: params[:team])
    if @team.nil?
      render plain: I18n.t('no_such_team', :url=>"#{request.env['HTTP_HOST']}/auth/slack") and return
    end
    @team_names =  session[:team].map{|h| h['name']}.uniq
    if @team_names.include? @team.name
      @items = Item.select('items.*, users.name as user_name, channels.name as channel_name').joins(:user).joins(:channel).where(team: @team).order(timestamp: :desc)
      render 'team'
    else
      redirect_to "/auth/slack"
    end
  end
end
