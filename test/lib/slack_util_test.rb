require "slack_util"

class SlackUtilTest < ActiveSupport::TestCase
  def setup
    @slack = SlackUtil.new "Invalid Token"
    @text = "amon Hello #test <#C02G4EZQB> <#C02G7EZQB> <@U02G4EZPX> <!channel> <!everyone> <!group> @mention !Fake"
    @team = teams(:default)
  end
  test 'get_channels should work' do
    channels = ["C02G4EZQB", "C02G7EZQB"]
    assert_equal channels, @slack.get_channels(@text)
  end

  test 'get_users should work' do
    users = ["U02G4EZPX"]
    assert_equal users, @slack.get_users(@text)
  end

  test 'parse should work' do
    final_string = "amon Hello #test #channel1 #channel2 @user1 @channel @everyone @group @mention !Fake"
    assert_equal final_string, @slack.parse(@text, @team.id)
  end

  test 'parse should work on sparse strings' do
    assert_equal "z", "z"
  end
end