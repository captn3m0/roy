class Item < ActiveRecord::Base
  belongs_to :team
  belongs_to :channel
  belongs_to :user
  def self.create_from_webhook(params)
    
    # Get the corresponding team
    team = Team.find_by_identifier(params[:team_id])
    return nil if team.nil?
    token = team.token
    # Use this token to pre-fill channel and user list
=begin
    channel = Channel.find_or_create_by(identifier: params[:channel_id]) do |c|
      c.name = params[:channel_name]
      c.team = team
    end

    user = User.find_or_create_by(identifier: params[:user_id]) do |u|
      u.name = params[:user_name]
      u.team = team
    end
=end


    text = params[:text].slice(params[:trigger_word].length..-1).lstrip

    item = Item.create({
      :team => team,
      :channel => channel,
      :user => user,
      :timestamp => params[:timestamp].to_i,
      :text => text
    })
  end
end
