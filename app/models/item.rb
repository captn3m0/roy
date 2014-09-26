require 'slack_util'

class Item < ActiveRecord::Base
  belongs_to :team
  belongs_to :channel
  belongs_to :user
  def self.create_from_webhook(params)
    
    # Get the corresponding team
    team = Team.find_by_identifier(params[:team_id])
    return nil if team.nil?
    token = team.token

    user = User.find_or_create_by(identifier: params[:user_id]) do |u|
      u.name = params[:user_name]
      u.team = team
    end

    text = params[:text].slice(params[:trigger_word].length..-1).lstrip

    # This is what was in the text
    slack = new SlackUtil(token)
    channel_identifiers = SlackUtil.get_channels(text)
    user_identifiers =    SlackUtil.get_users(text)

    item = Item.create({
      :team => team,
      :channel => channel,
      :user => user,
      :timestamp => params[:timestamp].to_i,
      :text => text
    })
  end
end
