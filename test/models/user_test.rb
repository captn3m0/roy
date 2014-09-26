require 'test_helper'
require 'deep_struct'

class UserTest < ActiveSupport::TestCase
  def setup
    @auth_hash = DeepStruct.new({
      provider: "slack",
      uid: "U02K78YST",
      info: {
        team: "Amon",
        user: "mako",
        team_id: "T02G4EZPV",
        user_id: "U02K78YST",
        name: nil
      },
      credentials: {
        token: "xoxp-00000000-0000000000-00000000-00000",
        expires: false
      },
      extra: {
        raw_info: {
          ok: true,
          url: "https://amon-hack.slack.com/",
          team: "Amon",
          user: "mako",
          team_id: "T02G4EZPV",
          user_id: "U02K78YST"
        }
      }
    })
  end

  test 'user should be created' do
    user = User.create_from_oauth(@auth_hash)
    assert user
    assert_equal @auth_hash['uid'], user.identifier
    assert_equal @auth_hash['credentials']['token'], user.token
    assert_equal 'amon-hack', user.team.name
    assert_equal @auth_hash['info']['team_id'], user.team.identifier
    assert_equal @auth_hash['info']['user_id'], user.identifier
  end
end