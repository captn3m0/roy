require 'slack'

class SlackUtil
  def initialize(token)
    @token = token
  end

  def get_channels(text)
    text.scan(/<#(C\w*)>/).flatten
  end

  def get_users(text)
    text.scan(/<@(U\w*)>/).flatten
   end

  def parse(text, team_id)
    # This is what we have in the database
    channels = Channel.where(team_id:team_id).index_by(&:identifier)
    users = User.where(team_id: team_id).index_by(&:identifier)
    text
      .gsub(/<#(C\w*)>/) do |match|
        channel_id = match.slice 2, match.length-3
        if channels.has_key? channel_id
          "##{channels[channel_id].name}"
        else
          match
        end
      end
      .gsub(/<@(U\w*)>/) do |match|
        user_id = match.slice 2, match.length-3
        if users.has_key? user_id
          "@#{users[user_id].name}"
        else
          match
        end
      end
      .gsub("<!channel>", "@channel")
      .gsub("<!group>", "@group")
      .gsub("<!everyone>", "@everyone")
  end
end