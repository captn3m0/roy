class TeamsController < ApplicationController

  before_action :setup, only: [:calendar, :index]

  def index
  end

  def setup
    session[:team] ||= []
    raise "No team specified" if params[:team].nil?
    @team = Team.find_by(name: params[:team])
    if @team.nil?
      render plain: I18n.t('no_such_team', :url=>"#{request.env['HTTP_HOST']}/auth/slack") and return
    end
    @team_names =  session[:team].map{|h| h['name']}.uniq
    if @team_names.include? @team.name
      @items = Item.select('items.*, users.name as user_name, channels.name as channel_name').joins(:user).joins(:channel).where(team: @team).order(timestamp: :desc)
    else
      redirect_to "/auth/slack"
    end

  end

  def calendar
    # Take the items and sort them into date-buckets
    @calendar = Hash.new
    for item in @items
      @calendar[item.created_at.to_date]||= []
      @calendar[item.created_at.to_date].push item
    end
    #render json: @calendar
  end
end