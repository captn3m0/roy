require 'slack_util'

class Item < ActiveRecord::Base
  belongs_to :team
  belongs_to :channel
  belongs_to :user
  def self.create_from_webhook(params)
    
    # Get the corresponding team
    team = Team.find_by_identifier(params[:team_id])
    return nil if team.nil?
    token = team.get_token

    user = User.find_or_create_by(identifier: params[:user_id]) do |u|
      u.name = params[:user_name]
      u.team = team
    end

    channel = Channel.find_or_create_by(identifier: params[:channel_id]) do |c|
      c.name = params[:channel_name]
      c.team = team
    end

    # This is what was in the text
    text = params[:text].slice(params[:trigger_word].length..-1).lstrip
    
    slack = SlackUtil.new token

    item = Item.create({
      :team => team,
      :channel => channel,
      :user => user,
      :timestamp => params[:timestamp].to_i,
      :text => slack.parse(text, team.id)
    })
  end
end
