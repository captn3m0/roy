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
end